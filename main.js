document.addEventListener("DOMContentLoaded", () => {
  // Form validation for registration
  const form = document.getElementById("registrationForm");
  
  if (form) {
    const validators = {
      full_name: val => /^[a-zA-Z ]{2,50}$/.test(val),
      gender: val => val !== "",
      date_of_birth: val => {
        const dob = new Date(val);
        const age = new Date().getFullYear() - dob.getFullYear();
        return age >= 18;
      },
      phone_number: val => /^09\d{9}$/.test(val),
      email: val => /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/.test(val),
      street: val => /^[a-zA-Z0-9\s#.,-]{5,100}$/.test(val),
      city: val => /^[a-zA-Z ]{2,50}$/.test(val),
      province: val => /^[a-zA-Z ]{2,50}$/.test(val),
      zip_code: val => /^\d{4}$/.test(val),
      country: val => /^[a-zA-Z ]+$/.test(val),
      username: val => /^\w{5,20}$/.test(val),
      password: val => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(val),
      confirm_password: () => {
        const pw = document.getElementById("password")?.value;
        const cpw = document.getElementById("confirm_password")?.value;
        return pw === cpw;
      }
    };

    Object.keys(validators).forEach(id => {
      const field = document.getElementById(id);
      if (!field) return;
      field.addEventListener("input", () => {
        const valid = validators[id](field.value);
        field.classList.toggle("is-valid", valid);
        field.classList.toggle("is-invalid", !valid);
      });
    });

    form.addEventListener("submit", e => {
      let allValid = true;
      Object.keys(validators).forEach(id => {
        const field = document.getElementById(id);
        if (field) {
          const valid = validators[id](field.value);
          field.classList.toggle("is-valid", valid);
          field.classList.toggle("is-invalid", !valid);
          if (!valid) allValid = false;
        }
      });
      if (!allValid) {
        e.preventDefault();
        alert("Please correct the errors before submitting.");
      }
    });
  }

  // Cart functionality
  initializeCart();
});

// Cart management functions
function initializeCart() {
  // Add to cart buttons
  document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.dataset.productId;
      const quantity = document.getElementById('quantity')?.value || 1;
      addToCart(productId, quantity);
    });
  });

  // Update cart display
  updateCartDisplay();
}

function addToCart(productId, quantity = 1) {
  fetch('cart_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `action=add&product_id=${productId}&quantity=${quantity}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      showNotification('Product added to cart!', 'success');
      updateCartDisplay();
    } else {
      showNotification(data.message || 'Error adding product to cart', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error adding product to cart', 'error');
  });
}

function updateQuantity(productId, change) {
  const quantityElement = document.getElementById(`quantity-${productId}`);
  const currentQuantity = parseInt(quantityElement.textContent);
  const newQuantity = Math.max(0, currentQuantity + change);
  
  if (newQuantity === 0) {
    removeFromCart(productId);
    return;
  }
  
  fetch('cart_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `action=update&product_id=${productId}&quantity=${newQuantity}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Update quantity display
      quantityElement.textContent = newQuantity;
      
      // Update item total
      const priceElement = document.querySelector(`[data-product-id="${productId}"] .text-primary.fw-bold`);
      const price = parseFloat(priceElement.textContent.replace('$', ''));
      const totalElement = document.getElementById(`total-${productId}`);
      totalElement.textContent = `$${(price * newQuantity).toFixed(2)}`;
      
      // Update cart summary
      updateCartSummary();
      updateCartDisplay();
    } else {
      showNotification(data.message || 'Error updating cart', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showNotification('Error updating cart', 'error');
  });
}

function removeFromCart(productId) {
  if (confirm('Are you sure you want to remove this item from your cart?')) {
    fetch('cart_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `action=remove&product_id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showNotification('Product removed from cart', 'success');
        // Remove the item from DOM
        const itemElement = document.querySelector(`[data-product-id="${productId}"]`);
        if (itemElement) {
          itemElement.remove();
        }
        updateCartSummary();
        updateCartDisplay();
        
        // Check if cart is empty and reload if needed
        setTimeout(() => {
          if (document.querySelectorAll('.cart-item').length === 0) {
            location.reload();
          }
        }, 500);
      } else {
        showNotification(data.message || 'Error removing product from cart', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Error removing product from cart', 'error');
    });
  }
}

function clearCart() {
  if (confirm('Are you sure you want to clear your entire cart? This action cannot be undone.')) {
    fetch('cart_handler.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: 'action=clear'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        showNotification('Cart cleared successfully', 'success');
        setTimeout(() => {
          location.reload();
        }, 1000);
      } else {
        showNotification(data.message || 'Error clearing cart', 'error');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showNotification('Error clearing cart', 'error');
    });
  }
}

function updateCartSummary() {
  // Calculate new totals
  let subtotal = 0;
  document.querySelectorAll('.cart-item').forEach(item => {
    const totalElement = item.querySelector('.item-total');
    const total = parseFloat(totalElement.textContent.replace('$', ''));
    subtotal += total;
  });
  
  const shipping = subtotal > 75 ? 0 : (subtotal > 0 ? 9.99 : 0);
  const tax = subtotal * 0.08;
  const total = subtotal + shipping + tax;
  
  // Update summary display
  document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
  
  const shippingElement = document.getElementById('shipping');
  if (shipping === 0 && subtotal > 0) {
    shippingElement.innerHTML = '<span class="text-success">FREE</span>';
  } else {
    shippingElement.textContent = `$${shipping.toFixed(2)}`;
  }
  
  document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
  document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}

function updateCartDisplay() {
  // Update cart badge/counter if exists
  fetch('cart_handler.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'action=count'
  })
  .then(response => response.json())
  .then(data => {
    const cartBadge = document.querySelector('.cart-badge');
    const cartCount = document.getElementById('cart-count');
    
    if (cartBadge && data.count !== undefined) {
      cartBadge.textContent = data.count;
      cartBadge.style.display = data.count > 0 ? 'inline' : 'none';
    }
    
    if (cartCount && data.count !== undefined) {
      cartCount.textContent = data.count;
      cartCount.style.display = data.count > 0 ? 'inline' : 'none';
    }
  })
  .catch(error => {
    console.error('Error updating cart display:', error);
  });
}

function showNotification(message, type = 'info') {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `alert alert-${type === 'error' ? 'danger' : type} notification-toast position-fixed`;
  notification.style.cssText = `
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: none;
    animation: slideInRight 0.3s ease;
  `;
  
  notification.innerHTML = `
    <div class="d-flex align-items-center">
      <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
      <span>${message}</span>
      <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
    </div>
  `;
  
  document.body.appendChild(notification);
  
  // Auto remove after 3 seconds
  setTimeout(() => {
    if (notification.parentElement) {
      notification.style.animation = 'slideOutRight 0.3s ease';
      setTimeout(() => {
        if (notification.parentElement) {
          notification.remove();
        }
      }, 300);
    }
  }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOutRight {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
`;
document.head.appendChild(style);