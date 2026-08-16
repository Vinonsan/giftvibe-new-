SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO banners (id, title, subtitle, image_path, link_url, placement, sort_order, status) VALUES
  (1, 'Find the Perfect Gift for Your Loved Ones', 'CRAFTED WITH LOVE|Exquisite curated gift boxes for every special moment|From LKR 4,500.00|EXPLORE NOW|bg-[#0B1528]', '/assets/images/hero_slide_1.jpg', '/shop', 'hero', 1, 'active'),
  (2, 'Luxury Bouquets to Brighten Up Their Day', 'FRESH & ELEGANT|Freshly picked luxury flowers delivered with care|From LKR 3,800.00|ORDER FLOWERS|bg-[#111111]', '/assets/images/hero_slide_2.jpg', '/shop?category=flowers', 'hero', 2, 'active'),
  (3, 'Assorted Gourmet Chocolates & Sweet Hampers', 'SWEET INDULGENCE|Premium chocolates and hampers for joyful celebrations|From LKR 5,200.00|SHOP SWEETS|bg-[#2C0A1A]', '/assets/images/hero_slide_3.jpg', '/shop?category=sweet-treats', 'hero', 3, 'active')
ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle), image_path=VALUES(image_path), link_url=VALUES(link_url), placement=VALUES(placement), sort_order=VALUES(sort_order), status=VALUES(status);

INSERT INTO categories (id, name, slug, description, image_path, sort_order, status) VALUES
  (1, 'Birthday Gifts', 'birthday-gifts', 'Joyful gifts for memorable birthdays.', '/assets/uploads/categories/2a2f4708c9b565432395c68e5afed21b.png', 1, 'active'),
  (2, 'Flowers', 'flowers', 'Fresh arrangements for every occasion.', '/assets/uploads/categories/5a727ebb72e5ffc96bc6301c84ec4d68.png', 2, 'active'),
  (3, 'Sweet Treats', 'sweet-treats', 'Chocolate and dessert gifts made to delight.', '/assets/uploads/categories/b7b7dcdb182e09d47e015527b4e89e5f.png', 3, 'active'),
  (4, 'Gift Boxes', 'gift-boxes', 'Thoughtfully curated premium gift boxes.', '/assets/uploads/categories/f903c2eb932f824a8523530badebe80d.png', 4, 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), image_path=VALUES(image_path), sort_order=VALUES(sort_order), status=VALUES(status);

INSERT INTO products (id, sku, name, slug, short_description, description, base_price, sale_price, unit, stock_quantity, is_featured, status) VALUES
  (1, 'GV-BOX-001', 'Celebration Gift Box', 'celebration-gift-box', 'A cheerful selection of treats and keepsakes.', 'A ready-to-gift celebration box curated for birthdays and joyful milestones.', 6500.00, 5900.00, 'box', 25, 1, 'active'),
  (2, 'GV-FLR-001', 'Blush Rose Bouquet', 'blush-rose-bouquet', 'An elegant bouquet of soft blush roses.', 'Fresh roses arranged by hand and wrapped for a beautiful presentation.', 4800.00, NULL, 'bouquet', 18, 1, 'active'),
  (3, 'GV-SWT-001', 'Chocolate Indulgence Box', 'chocolate-indulgence-box', 'Premium chocolates for a sweet celebration.', 'A rich assortment of chocolates, presented in a premium keepsake box.', 5200.00, 4750.00, 'box', 30, 1, 'active'),
  (4, 'GV-HMP-001', 'Golden Moments Hamper', 'golden-moments-hamper', 'A luxurious hamper for life’s special moments.', 'A generous hamper combining gourmet treats and thoughtful celebration pieces.', 8900.00, 8250.00, 'hamper', 12, 1, 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), short_description=VALUES(short_description), description=VALUES(description), base_price=VALUES(base_price), sale_price=VALUES(sale_price), stock_quantity=VALUES(stock_quantity), is_featured=VALUES(is_featured), status=VALUES(status);

INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary) VALUES
  (1, '/assets/uploads/products/1e6a4353627593a79329817d77ce91ca.png', 'Celebration gift box', 1, 1),
  (2, '/assets/uploads/products/2c0c24f03a4fcd3db32d4c075a164a95.png', 'Blush rose bouquet', 1, 1),
  (3, '/assets/uploads/products/501abe1ce41e1bd6e4ae2eb5a6a647bc.png', 'Chocolate indulgence box', 1, 1),
  (4, '/assets/uploads/products/7a80ae0588384d0c6674d30af24ec584.png', 'Golden moments hamper', 1, 1);

INSERT IGNORE INTO product_categories (product_id, category_id) VALUES
  (1,1), (1,4), (2,2), (3,3), (3,4), (4,1), (4,3), (4,4);

INSERT INTO combos (id, name, slug, description, price, status) VALUES
  (1, 'Birthday Surprise Combo', 'birthday-surprise-combo', 'A festive gift box and sweet treat pairing for a wonderful birthday surprise.', 9900.00, 'active'),
  (2, 'Love & Roses Combo', 'love-and-roses-combo', 'Fresh roses paired with premium chocolates for someone special.', 8950.00, 'active'),
  (3, 'Grand Celebration Combo', 'grand-celebration-combo', 'A generous collection made for unforgettable celebrations.', 13900.00, 'active'),
  (4, 'Sweet Moments Combo', 'sweet-moments-combo', 'A charming assortment of treats and thoughtful gifts.', 7800.00, 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), price=VALUES(price), status=VALUES(status);

INSERT INTO combo_images (combo_id, image_path, alt_text, sort_order, is_primary) VALUES
  (1, '/assets/uploads/combos/0b3eee47ccfbcf96498ea09eb1e44b95.png', 'Birthday surprise combo', 1, 1),
  (2, '/assets/uploads/combos/364f68e6ae160a99c2db1da013bbed0b.png', 'Love and roses combo', 1, 1),
  (3, '/assets/uploads/combos/85c39f914ac0aa8655ddda0a99b32e12.png', 'Grand celebration combo', 1, 1),
  (4, '/assets/uploads/combos/ac6dde5186fab7b46bd9cb58709fecd0.jpg', 'Sweet moments combo', 1, 1);

INSERT IGNORE INTO combo_products (combo_id, product_id, sort_order) VALUES
  (1,1,1), (1,3,2), (2,2,1), (2,3,2), (3,1,1), (3,2,2), (3,4,3), (4,1,1), (4,3,2);

INSERT INTO homepage_ctas (placement, badge, title, description, button_label, link_url, image_path, background_color, overlay_opacity, sort_order, status) VALUES
  ('home_after_categories', 'Made for every moment', 'Turn thoughtful moments into lasting memories', 'Choose a curated gift or let us help you create something personal for someone special.', 'Explore gifts', '/shop', '/assets/uploads/cta/62cd254f1c8fc6a8dd159cadeaa2af77.png', '#102E50', 55, 1, 'active'),
  ('home_between_products_combos', 'Better together', 'Discover ready-to-gift celebration combos', 'Beautifully matched favourites, wrapped together and ready to make their day.', 'Shop combos', '/combos', '/assets/uploads/cta/6a9d7cc81c852254e2d3ac5b4c0405c7.png', '#0B182E', 55, 2, 'active');

INSERT INTO testimonials (reviewer_name, reviewer_role, avatar_path, title, review_text, rating, source, status, sort_order) VALUES
  ('Kugan', 'Birthday shopper', '/assets/uploads/testimonials/kugan.jpg', 'Beautifully presented', 'The gift arrived right on time and every detail looked wonderful. It made the celebration extra special.', 5, 'admin', 'approved', 1),
  ('Vasuki', 'GiftVibe customer', '/assets/uploads/testimonials/vasuki.jpg', 'A lovely surprise', 'Ordering was simple, the presentation was elegant, and the recipient absolutely loved it.', 5, 'admin', 'approved', 2),
  ('Yazhini', 'Anniversary shopper', '/assets/uploads/testimonials/yazhini.jpg', 'Thoughtful from start to finish', 'The combination was beautifully curated and the service was warm and helpful throughout.', 5, 'admin', 'approved', 3);

INSERT INTO testimonials (id, reviewer_name, reviewer_role, avatar_path, title, review_text, rating, source, status, sort_order) VALUES
  (4, 'Nivetha Raj', 'Surprise gift shopper', NULL, 'Exactly what I hoped for', 'The packaging felt premium and the handwritten message made the gift feel genuinely personal.', 5, 'admin', 'approved', 4),
  (5, 'Arun Kumar', 'Corporate gifting customer', NULL, 'Effortless and reliable', 'GiftVibe helped us arrange several gifts quickly, and every recipient was delighted with the presentation.', 5, 'admin', 'approved', 5),
  (6, 'Shalini Devi', 'Flower bouquet customer', NULL, 'Fresh, elegant and on time', 'The flowers looked even better than the photos and arrived beautifully wrapped at the perfect time.', 5, 'admin', 'approved', 6)
ON DUPLICATE KEY UPDATE reviewer_name=VALUES(reviewer_name), reviewer_role=VALUES(reviewer_role), title=VALUES(title), review_text=VALUES(review_text), rating=VALUES(rating), status=VALUES(status), sort_order=VALUES(sort_order);

INSERT INTO faqs (question, answer, category, sort_order, status) VALUES
  ('How quickly can my gift be delivered?', 'Delivery timing depends on the destination and selected product. Available delivery options are shown during checkout.', 'Delivery', 1, 'active'),
  ('Can I include a personal message?', 'Yes. You can add a personal message while ordering and we will include it with your gift.', 'Gifting', 2, 'active'),
  ('Can I customise a gift box?', 'Yes. Contact us with your preferred items, theme, budget and delivery date, and our team will help curate it.', 'Custom gifts', 3, 'active'),
  ('How should I care for a flower bouquet?', 'Keep flowers in fresh water, trim the stems at an angle and place them away from direct sunlight and heat.', 'Flowers', 4, 'active'),
  ('What payment methods are accepted?', 'Available payment methods are displayed securely during checkout.', 'Payments', 5, 'active');

SET FOREIGN_KEY_CHECKS = 1;
