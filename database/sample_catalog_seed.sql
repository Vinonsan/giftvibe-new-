USE `dbof-giftvibelk`;

INSERT IGNORE INTO categories (id, name, slug, description, image_path, sort_order, status, meta_title, meta_description) VALUES
  (1, 'Birthday Gifts', 'birthday-gifts', 'Bright gift boxes, flowers, and treats for birthdays across Sri Lanka.', 'public/assets/images/categories/birthday-gifts.png', 1, 'active', 'Birthday Gifts Sri Lanka', 'Shop birthday gifts with GiftVibe.lk delivery.'),
  (2, 'Luxury Hampers', 'luxury-hampers', 'Premium hampers with chocolates, florals, keepsakes, and handwritten notes.', 'public/assets/images/categories/luxury-hampers.png', 2, 'active', 'Luxury Hampers Sri Lanka', 'Premium gift hampers delivered by GiftVibe.lk.'),
  (3, 'Flowers & Chocolates', 'flowers-chocolates', 'Classic flowers and chocolate pairings for thoughtful same-week gifting.', 'public/assets/images/categories/flowers-chocolates.png', 3, 'active', 'Flowers and Chocolates Sri Lanka', 'Order flowers and chocolates from GiftVibe.lk.');

INSERT IGNORE INTO products (id, sku, name, slug, short_description, description, base_price, sale_price, cost_price, stock_quantity, low_stock_threshold, is_featured, status, meta_title, meta_description) VALUES
  (1, 'GV-BDAY-2026', 'Birthday Glow Gift Box', 'birthday-glow-gift-box', 'A polished birthday box with florals, sweets, and a personal note.', 'Designed for warm birthday surprises with a premium unboxing moment.', 8500.00, 7900.00, 4700.00, 18, 5, 1, 'active', 'Birthday Glow Gift Box', 'Send a premium birthday gift box in Sri Lanka.'),
  (2, 'GV-LUX-2026', 'Luxe Celebration Hamper', 'luxe-celebration-hamper', 'A premium hamper for anniversaries, promotions, and milestone moments.', 'A modern luxury hamper arranged with treats, keepsakes, and elegant wrapping.', 14500.00, NULL, 8600.00, 9, 4, 1, 'active', 'Luxe Celebration Hamper', 'Luxury celebration hamper from GiftVibe.lk.'),
  (3, 'GV-FLOW-2026', 'Rose & Chocolate Moment', 'rose-chocolate-moment', 'Fresh roses paired with chocolates for a classic romantic gesture.', 'A dependable flowers-and-chocolates gift for meaningful delivery moments.', 6200.00, 5900.00, 3400.00, 22, 6, 0, 'active', 'Rose and Chocolate Gift', 'Order roses and chocolates in Sri Lanka.');

INSERT IGNORE INTO product_categories (product_id, category_id) VALUES
  (1, 1),
  (2, 2),
  (3, 3);

INSERT IGNORE INTO product_images (product_id, image_path, alt_text, sort_order, is_primary) VALUES
  (1, 'public/assets/images/hero_gift_box.jpg', 'Birthday Glow Gift Box with flowers and sweets', 1, 1),
  (2, 'public/assets/images/hero_gift_box.jpg', 'Luxury hamper gift box arranged for delivery', 1, 1),
  (3, 'public/assets/images/hero_gift_box.jpg', 'Rose and chocolate gift arrangement', 1, 1);
