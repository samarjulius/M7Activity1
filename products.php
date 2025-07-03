<?php
// Products file - no session management here

// Product categories
$categories = [
    'cleansers' => 'Cleansers',
    'serums' => 'Serums',
    'moisturizers' => 'Moisturizers',
    'sunscreens' => 'Sunscreens',
    'toners' => 'Toners',
    'masks' => 'Face Masks',
    'treatments' => 'Treatments',
    'eye-care' => 'Eye Care'
];

// Skincare products database
$products = [
    [
        'id' => 1,
        'name' => 'Rich Night Cream',
        'short_description' => 'Maintains skin hydration overnight while gently soothing skin.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation. Ideal for sensitive skin.',
        'price' => 24.99,
        'image' => 'products/Rich Night Cream.png',
        'rating' => 4.5,
        'category' => 'cleansers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 2,
        'name' => 'Brightening Night Comfort Cream',
        'short_description' => 'Powerful antioxidant serum that brightens and evens skin tone.',
        'description' => 'The Brightening Night Comfort Cream is a skin-cushioning cream for tireless moms that brightens and evens out skin tone, without causing skin irritation or weakening the skin barrier.',
        'price' => 45.99,
        'image' => 'products/Brightening Night Comfort Cream.webp',
        'rating' => 4.8,
        'category' => 'serums',
        'features' => [
            '20% L-Ascorbic Acid',
            'Brightens complexion',
            'Reduces dark spots',
            'Antioxidant protection',
            'Includes Hyaluronic Acid'
        ],
        'ingredients' => 'Aqua, L-Ascorbic Acid, Sodium Hyaluronate, Tocopherol (Vitamin E), Ferulic Acid, Propylene Glycol, Sodium Hydroxide, Disodium EDTA, Phenoxyethanol.',
        'usage' => 'Apply 2-3 drops to clean, dry skin in the morning. Gently pat into skin and follow with moisturizer and sunscreen. Start with once daily and gradually increase to twice daily as tolerated.',
        'in_stock' => true
    ],
    [
        'id' => 3,
        'name' => 'Healthy Renew Moisturizing Day Cream SPF 30',
        'short_description' => 'Gentle Baby Lotion is designed to soothe and nourish, while moisturizing and protecting your skin from dryness.',
        'description' => 'Gentle Baby Lotion is designed to soothe and nourish, while moisturizing and protecting your skin from dryness.',
        'price' => 38.50,
        'image' => 'products/Healthy Renew Moisturizing Day Cream SPF 30.jpg',
        'rating' => 4.6,
        'category' => 'moisturizers',
        'features' => [
            'Contains Retinol and Peptides',
            'Deep overnight hydration',
            'Anti-aging benefits',
            'Repairs and rejuvenates',
            'Rich, luxurious texture'
        ],
        'ingredients' => 'Aqua, Glycerin, Caprylic/Capric Triglyceride, Cetearyl Alcohol, Retinol, Palmitoyl Pentapeptide-4, Ceramide NP, Squalane, Dimethicone, Sodium Hyaluronate, Tocopherol.',
        'usage' => 'Apply a generous amount to clean face and neck every evening. Gently massage until fully absorbed. Use sunscreen during the day when using this product as it contains Retinol.',
        'in_stock' => true
    ],
    [
        'id' => 4,
        'name' => 'Advanced Protection Cream',
        'short_description' => 'Maintains skin hydration overnight while gently soothing skin.',
        'description' => 'Protect your skin from harmful UV rays with our Daily SPF 50 Sunscreen. This lightweight, non-greasy formula provides broad-spectrum protection while moisturizing your skin. Perfect for daily use under makeup or on its own.',
        'price' => 22.99,
        'image' => 'products/Advanced Protection Cream.png',
        'rating' => 4.7,
        'category' => 'sunscreens',
        'features' => [
            'SPF 50 broad-spectrum protection',
            'Lightweight, non-greasy formula',
            'Water-resistant for 80 minutes',
            'Suitable for daily use',
            'Works well under makeup'
        ],
        'ingredients' => 'Avobenzone 3%, Homosalate 10%, Octisalate 5%, Octocrylene 10%, Aqua, Glycerin, Dimethicone, Cetearyl Alcohol, Glyceryl Stearate, Sodium Hyaluronate.',
        'usage' => 'Apply generously to all exposed skin 15 minutes before sun exposure. Reapply at least every 2 hours and after swimming, sweating, or toweling off.',
        'in_stock' => true
    ],
    [
        'id' => 5,
        'name' => 'Hydrating Eye Cream Serum',
        'short_description' => 'Hydrates delicate skin around the eye area to improve radiance.',
        'description' => 'Our Balancing Toner is formulated with witch hazel and niacinamide to gently balance your skin\'s pH while minimizing pores and controlling oil production. This alcohol-free formula is suitable for all skin types and helps prepare your skin for the next steps in your routine.',
        'price' => 19.99,
        'image' => 'products/Hydrating Eye Cream Serum.png',
        'rating' => 4.3,
        'category' => 'serum',
        'features' => [
            'Alcohol-free formula',
            'Balances skin pH',
            'Minimizes pores',
            'Controls oil production',
            'Suitable for all skin types'
        ],
        'ingredients' => 'Aqua, Hamamelis Virginiana (Witch Hazel) Water, Niacinamide, Glycerin, Panthenol, Allantoin, Sodium PCA, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'After cleansing, apply to a cotton pad and gently sweep over face and neck, avoiding the eye area. Follow with serum and moisturizer. Use morning and evening.',
        'in_stock' => true
    ],
    [
        'id' => 6,
        'name' => 'Baby Wash and Shampoo',
        'short_description' => 'Tear free formula gently cleanses and keeps the delicate skin and hair of the baby hydrated without drying.',
        'description' => 'This weekly treatment mask is formulated with bentonite clay and activated charcoal to deeply cleanse pores and remove impurities. Enhanced with tea tree oil for its antibacterial properties, this mask leaves your skin feeling refreshed and clarified.',
        'price' => 28.99,
        'image' => 'products/Baby Wash and Shampoo.png',
        'rating' => 4.4,
        'category' => 'masks',
        'features' => [
            'Contains bentonite clay',
            'Activated charcoal for deep cleansing',
            'Tea tree oil for antibacterial action',
            'Draws out impurities',
            'Weekly treatment'
        ],
        'ingredients' => 'Bentonite, Aqua, Kaolin, Activated Charcoal, Melaleuca Alternifolia (Tea Tree) Leaf Oil, Glycerin, Xanthan Gum, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply an even layer to clean, dry skin, avoiding the eye area. Leave on for 10-15 minutes until dry. Rinse thoroughly with warm water. Use 1-2 times per week.',
        'in_stock' => true
    ],
    [
        'id' => 7,
        'name' => 'Daily Facial Moisturizer',
        'short_description' => 'This foaming gel cleanser is formulated with 2% Salicylic Acid, the maximum strength OTC formula.',
        'description' => 'Our Anti-Aging Eye Cream is specifically formulated for the delicate skin around your eyes. With peptides, caffeine, and hyaluronic acid, this cream helps reduce the appearance of fine lines, puffiness, and dark circles while providing intense hydration.',
        'price' => 42.99,
        'image' => 'products/Daily Facial Moisturizer.webp',
        'rating' => 4.6,
        'category' => 'eye-care',
        'features' => [
            'Peptides for anti-aging',
            'Caffeine reduces puffiness',
            'Hyaluronic acid for hydration',
            'Reduces fine lines',
            'Targets dark circles'
        ],
        'ingredients' => 'Aqua, Glycerin, Caprylic/Capric Triglyceride, Caffeine, Palmitoyl Pentapeptide-4, Sodium Hyaluronate, Dimethicone, Cetearyl Alcohol, Tocopherol.',
        'usage' => 'Gently pat a small amount around the eye area using your ring finger. Use morning and evening after cleansing and before moisturizer.',
        'in_stock' => true
    ],
    [
        'id' => 8,
        'name' => 'Protective Lip Balm',
        'short_description' => 'Protective Lip Balm offers effective barrier against moisture loss, as well as protection from both UVA and UVB.',
        'description' => 'This concentrated serum contains 10% niacinamide to help minimize the appearance of pores, regulate oil production, and improve overall skin texture. Enhanced with zinc for additional pore-refining benefits, this treatment is perfect for oily and combination skin types.',
        'price' => 32.99,
        'image' => 'products/Protective Lip Balm.webp',
        'rating' => 4.5,
        'category' => 'treatments',
        'features' => [
            '10% Niacinamide concentration',
            'Minimizes pore appearance',
            'Regulates oil production',
            'Improves skin texture',
            'Contains zinc'
        ],
        'ingredients' => 'Aqua, Niacinamide, Zinc PCA, Glycerin, Propanediol, Tamarindus Indica Seed Gum, Xanthan Gum, Isoceteth-20, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a few drops to clean skin before moisturizer. Start with once daily and gradually increase to twice daily as tolerated. Use morning and/or evening.',
        'in_stock' => true
    ],
    [
        'id' => 9,
        'name' => 'Moisturizing Lotion',
        'short_description' => 'Pore-minimizing serum for smoother skin texture.',
        'description' => 'This concentrated serum contains 10% niacinamide to help minimize the appearance of pores, regulate oil production, and improve overall skin texture. Enhanced with zinc for additional pore-refining benefits, this treatment is perfect for oily and combination skin types.',
        'price' => 25.33,
        'image' => 'products/Moisturizing Lotion.jpg',
        'rating' => 4.5,
        'category' => 'treatments',
        'features' => [
            '10% Niacinamide concentration',
            'Minimizes pore appearance',
            'Regulates oil production',
            'Improves skin texture',
            'Contains zinc'
        ],
        'ingredients' => 'Aqua, Niacinamide, Zinc PCA, Glycerin, Propanediol, Tamarindus Indica Seed Gum, Xanthan Gum, Isoceteth-20, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a few drops to clean skin before moisturizer. Start with once daily and gradually increase to twice daily as tolerated. Use morning and/or evening.',
        'in_stock' => true
    ],
    [
        'id' => 10,
        'name' => 'Gentle Skin Cleansing Cloths',
        'short_description' => 'Pre-moistened cloths—perfect for at home, travel or after exercising.',
        'description' => 'This concentrated serum contains 10% niacinamide to help minimize the appearance of pores, regulate oil production, and improve overall skin texture. Enhanced with zinc for additional pore-refining benefits, this treatment is perfect for oily and combination skin types.',
        'price' => 25.33,
        'image' => 'products/Gentle Skin Cleansing Cloths.png',
        'rating' => 4.5,
        'category' => 'treatments',
        'features' => [
            '10% Niacinamide concentration',
            'Minimizes pore appearance',
            'Regulates oil production',
            'Improves skin texture',
            'Contains zinc'
        ],
        'ingredients' => 'Aqua, Niacinamide, Zinc PCA, Glycerin, Propanediol, Tamarindus Indica Seed Gum, Xanthan Gum, Isoceteth-20, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a few drops to clean skin before moisturizer. Start with once daily and gradually increase to twice daily as tolerated. Use morning and/or evening.',
        'in_stock' => true
    ],
    [
        'id' => 11,
        'name' => 'Gentle Foaming Cleanser',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Gentle Foaming Cleanser.avif',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 12,
        'name' => 'Sun SPF 50 Face and Body Light Gel',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Sun SPF 50 Face and Body Light Gel.jpg',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 13,
        'name' => 'Moisturizer for Acne Scars and Redness',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Moisturizer for Acne Scars and Redness.webp',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 14,
        'name' => 'Gentle Clear Acne Treatment Serum',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Gentle Clear Acne Treatment Serum.jpg',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 15,
        'name' => 'Pore Clearing Acne Cleanser Pump',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Pore Clearing Acne Cleanser Pump.webp',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 16,
        'name' => 'Sheer Sunscreen Lotion',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Sheer Sunscreen Lotion.webp',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 17,
        'name' => 'Moisturizing Cream Jar',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Moisturising Cream Jar.webp',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 18,
        'name' => 'Dark Spot-Defeating Face Cream',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Dark Spot-Defeating Face Cream.jpg',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 19,
        'name' => 'Oil Control Foam Wash',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Oil Control Foam Wash.jpg',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],
    [
        'id' => 20,
        'name' => 'Healthy Renew Moisturizing Day Cream SPF 30',
        'short_description' => 'A mild foaming facial cleanser that rinses away dirt, oil and makeup without stripping skins natural moisture balance.',
        'description' => 'Thoroughly remove dirt, excess oil and makeup without irritation.',
        'price' => 24.99,
        'image' => 'products/Healthy Renew Moisturizing Day Cream SPF 30.jpg',
        'rating' => 4.5,
        'category' => 'moisturizers',
        'features' => [
            'Soap-free formula',
            'Suitable for all skin types',
            'Removes makeup effectively',
            'pH-balanced',
            'Dermatologist tested'
        ],
        'ingredients' => 'Aqua, Sodium Cocoyl Glycinate, Glycerin, Coco-Betaine, Sodium Chloride, Chamomilla Recutita Extract, Aloe Barbadensis Leaf Juice, Panthenol, Allantoin, Citric Acid, Phenoxyethanol, Ethylhexylglycerin.',
        'usage' => 'Apply a small amount to wet hands and work into a rich lather. Gently massage over face and neck, avoiding the eye area. Rinse thoroughly with warm water. Use morning and evening for best results.',
        'in_stock' => true
    ],

];

// Helper functions
function getProductById($id) {
    global $products;
    foreach ($products as $product) {
        if ($product['id'] == $id) {
            return $product;
        }
    }
    return null;
}

function getProductsByCategory($category) {
    global $products;
    return array_filter($products, function($product) use ($category) {
        return $product['category'] === $category;
    });
}

function getFeaturedProducts($limit = 3) {
    global $products;
    $featured = $products;
    usort($featured, function($a, $b) {
        return $b['rating'] <=> $a['rating'];
    });
    return array_slice($featured, 0, $limit);
}
?>