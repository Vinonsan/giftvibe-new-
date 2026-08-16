-- =====================================================================
-- GiftVibe — Sample products & categories (varied variety)
-- Run with:  mysql -u root giftvibe_dev < database/sample-products.sql
-- Safe to re-run (idempotent: ON DUPLICATE KEY UPDATE / INSERT IGNORE).
-- =====================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── Categories ────────────────────────────────────────────────────────
INSERT INTO categories (id, name, slug, description, image_path, sort_order, status) VALUES
  (2, 'Fresh Bouquets',      'fresh-bouquets',      'Hand-tied fresh flower bouquets for every occasion.',     '/assets/uploads/categories/5a727ebb72e5ffc96bc6301c84ec4d68.png', 2, 'active'),
  (3, 'Gift Hampers',        'gift-hampers',        'Curated hampers filled with thoughtful goodies.',          '/assets/uploads/categories/6bf8fe62c203b3a638352deade0dc2bf.png', 3, 'active'),
  (4, 'Chocolates & Sweets', 'chocolates-sweets',   'Premium chocolates and sweet treats made to delight.',     '/assets/uploads/categories/2a2f4708c9b565432395c68e5afed21b.png', 4, 'active'),
  (5, 'Teddy Bears & Plush', 'teddy-bears-plush',   'Soft and cuddly plush toys perfect for gifting.',          '/assets/uploads/categories/5a727ebb72e5ffc96bc6301c84ec4d68.png', 5, 'active'),
  (6, 'Gift Boxes',          'gift-boxes',          'Elegant gift boxes ready to surprise someone special.',    '/assets/uploads/categories/6bf8fe62c203b3a638352deade0dc2bf.png', 6, 'active'),
  (7, 'Home & Decor',        'home-decor',          'Cozy decor pieces to make a house a home.',                '/assets/uploads/categories/2a2f4708c9b565432395c68e5afed21b.png', 7, 'active')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), image_path=VALUES(image_path), sort_order=VALUES(sort_order), status=VALUES(status);

-- ── Products ──────────────────────────────────────────────────────────
INSERT INTO products (id, sku, name, slug, short_description, description, base_price, sale_price, cost_price, procurement_type, offer_type, offer_value, unit, stock_quantity, low_stock_threshold, is_featured, status, meta_title, meta_description, search_keywords) VALUES
  (6,  'GV-SUNFLOWER-BQ',  'Sunflower Smile Bouquet',        'sunflower-smile-bouquet',      'A cheerful bunch of sunflowers to brighten any day.', 'Hand-tied sunflowers wrapped in kraft paper, finished with a free greeting card. Perfect for birthdays, thank-yous and housewarmings.', 5500.00, NULL, 3150.00, 'handcrafted', 'none', NULL, 'bouquet', 8, 3, 1, 'active', 'Sunflower Smile Bouquet | GiftVibe', 'Fresh hand-tied sunflower bouquet delivered across Sri Lanka.', 'sunflower bouquet, yellow flowers, birthday flowers, bright bouquet, flowers colombo'),
  (7,  'GV-ROSE-VELVET',   'Red Velvet Rose Bouquet',        'red-velvet-rose-bouquet',      'Velvet-textured red roses, hand-arranged for romance.', 'A dozen premium red roses with eucalyptus, hand-wrapped and ready to impress. Ideal for anniversaries and Valentine''s Day.', 7800.00, 7020.00, 4250.00, 'handcrafted', 'percentage', 10.00, 'bouquet', 15, 5, 1, 'active', 'Red Velvet Rose Bouquet | GiftVibe', 'Premium red roses hand-arranged and delivered across Sri Lanka.', 'red roses, rose bouquet, anniversary flowers, valentine flowers, romantic gift'),
  (8,  'GV-TULIP-BUNCH',   'Tulip & Baby Breath Bunch',      'tulip-baby-breath-bunch',      'Pastel tulips with baby''s breath for a soft, delicate look.', 'A delicate bunch of pastel tulips paired with baby''s breath, wrapped in soft paper. A gentle choice for any occasion.', 4500.00, NULL, 2600.00, 'purchased', 'none', NULL, 'bunch', 4, 5, 0, 'active', 'Tulip & Baby Breath Bunch | GiftVibe', 'Soft pastel tulip bunch with baby''s breath delivered fresh.', 'tulips, pastel flowers, spring flowers, delicate bouquet, tulip bunch'),
  (9,  'GV-CHOC-HAMPER',   'Chocolate Lover''s Hamper',      'chocolate-lovers-hamper',      'A decadent hamper packed with gourmet chocolates.', 'Belgian truffles, pralines, artisan chocolate bars and a box of hot chocolate mix, all presented in a premium hamper.', 8900.00, NULL, 5200.00, 'purchased', 'none', NULL, 'hamper', 6, 3, 1, 'active', 'Chocolate Lover''s Hamper | GiftVibe', 'A gourmet chocolate hamper for true chocolate lovers.', 'chocolate hamper, chocolate gift, gourmet chocolate, sweets hamper, chocolate box'),
  (10, 'GV-SPA-HAMPER',    'Spa & Serenity Hamper',          'spa-serenity-hamper',          'A relaxing spa-day hamper of self-care essentials.', 'Scented candles, bath salts, a plush robe and herbal teas for a calming at-home spa day.', 12000.00, 10800.00, 7800.00, 'purchased', 'fixed', 1200.00, 'hamper', 3, 2, 0, 'active', 'Spa & Serenity Hamper | GiftVibe', 'Luxury self-care spa hamper delivered across Sri Lanka.', 'spa hamper, self care gift, relaxation gift, bath hamper, wellness gift'),
  (11, 'GV-BDAY-HAMPER',   'Birthday Bash Hamper',           'birthday-bash-hamper',         'Party-ready treats and goodies for the birthday star.', 'A fun-filled hamper of sweet treats, snacks and a celebration banner — everything for a memorable birthday.', 6500.00, NULL, 3900.00, 'handcrafted', 'none', NULL, 'hamper', 12, 5, 1, 'active', 'Birthday Bash Hamper | GiftVibe', 'A cheerful birthday hamper full of treats and surprises.', 'birthday hamper, birthday gift, party hamper, celebration hamper, birthday treats'),
  (12, 'GV-BELGIAN-CHOC',  'Belgian Chocolate Box',          'belgian-chocolate-box',        'A curated box of Belgian chocolates.', 'A rich assortment of Belgian chocolates — truffles, pralines and ganache — in a keepsake gift box.', 4200.00, NULL, 2100.00, 'purchased', 'none', NULL, 'box', 25, 5, 1, 'active', 'Belgian Chocolate Box | GiftVibe', 'Premium Belgian chocolates in a luxury gift box.', 'belgian chocolate, chocolate box, luxury chocolate, sweet gift, pralines'),
  (13, 'GV-MACARON-SET',   'Assorted Macaron Set',           'assorted-macaron-set',         'A colourful box of twelve assorted French macarons.', 'Twelve freshly made French macarons in a rainbow of flavours, gift-boxed for any celebration.', 3600.00, NULL, 1800.00, 'handcrafted', 'none', NULL, 'set', 18, 5, 0, 'active', 'Assorted Macaron Set | GiftVibe', 'A box of twelve assorted French macarons, handcrafted.', 'macaron, french dessert, macaron set, sweet gift box, macarons colombo'),
  (14, 'GV-HONEY-JAR',     'Honey & Nut Delight Jar',        'honey-nut-delight-jar',        'Raw honey with roasted nuts in a gift-ready jar.', 'Pure raw honey layered with roasted almonds and cashews in a beautiful gift-ready jar.', 2800.00, NULL, 1500.00, 'handcrafted', 'none', NULL, 'jar', 0, 3, 0, 'active', 'Honey & Nut Delight Jar | GiftVibe', 'Raw honey with roasted nuts in a gift-ready jar.', 'honey jar, nuts gift, food gift, healthy gift, honey gift'),
  (15, 'GV-TEDDY-GIANT',   'Cuddly Teddy Bear (Giant)',      'cuddly-teddy-bear-giant',      'A giant soft teddy bear that hugs back.', 'Extra-soft giant teddy (90cm) with a satin bow. Perfect for birthdays, anniversaries and proposals.', 9500.00, 8550.00, 5600.00, 'purchased', 'percentage', 10.00, 'piece', 7, 2, 1, 'active', 'Cuddly Teddy Bear (Giant) | GiftVibe', 'A giant 90cm soft teddy bear, perfect for gifting.', 'teddy bear, giant teddy, plush toy, soft toy, cuddly gift'),
  (16, 'GV-BUNNY-PAIR',    'Mini Bunny Plush Pair',          'mini-bunny-plush-pair',        'Two adorable mini bunnies — one for you, one for them.', 'A pair of soft mini bunny plush toys in pastel shades, perfect for Easter or a cute surprise.', 3900.00, NULL, 2100.00, 'purchased', 'none', NULL, 'pair', 20, 5, 0, 'active', 'Mini Bunny Plush Pair | GiftVibe', 'A cute pair of mini bunny plush toys.', 'bunny, plush pair, easter gift, soft toy pair, bunny toy'),
  (17, 'GV-JEWEL-BOX',     'Rose Gold Jewellery Gift Box',   'rose-gold-jewellery-gift-box', 'An elegant box ready for a jewellery surprise.', 'Rose gold keepsake box with velvet lining — pair it with a necklace or ring for a memorable reveal.', 5400.00, NULL, 2900.00, 'handcrafted', 'none', NULL, 'box', 5, 3, 1, 'active', 'Rose Gold Jewellery Gift Box | GiftVibe', 'Elegant rose gold jewellery gift box with velvet lining.', 'jewellery box, rose gold box, ring box, keepsake box, gift box'),
  (18, 'GV-ANNIV-KEEP',    'Anniversary Keepsake Box',       'anniversary-keepsake-box',     'A personalised keepsake box for celebrating love.', 'A personalised wooden keepsake box with space for photos and letters — a heartfelt anniversary gift.', 6100.00, NULL, 3500.00, 'handcrafted', 'none', NULL, 'box', 9, 3, 0, 'active', 'Anniversary Keepsake Box | GiftVibe', 'A personalised anniversary keepsake box.', 'anniversary box, keepsake gift, personalised gift box, memory box, anniversary gift'),
  (19, 'GV-PANDA-AURORA',  'Aurora Panda Night Lamp',        'aurora-panda-night-lamp',      'A dreamy aurora night lamp in a cute panda design.', 'A soft-glow aurora night lamp in an adorable panda design — a cosy addition to any desk or bedside.', 7500.00, 6900.00, 4100.00, 'purchased', 'percentage', 8.00, 'piece', 10, 3, 1, 'active', 'Aurora Panda Night Lamp | GiftVibe', 'A cute aurora panda night lamp for a dreamy glow.', 'panda lamp, night lamp, aurora lamp, home decor, desk light'),
  (20, 'GV-HOLIDAY-BOX',   'Holiday Surprise Box',           'holiday-surprise-box',         'A festive box of seasonal treats (draft).', 'A seasonal surprise box being finalised for the upcoming holidays — coming soon.', 5200.00, NULL, 2800.00, 'handcrafted', 'none', NULL, 'box', 0, 3, 0, 'draft', 'Holiday Surprise Box | GiftVibe', 'A festive holiday surprise box (draft).', 'holiday box, festive gift, surprise box, seasonal gift')
ON DUPLICATE KEY UPDATE sku=VALUES(sku), name=VALUES(name), slug=VALUES(slug), short_description=VALUES(short_description), description=VALUES(description), base_price=VALUES(base_price), sale_price=VALUES(sale_price), cost_price=VALUES(cost_price), procurement_type=VALUES(procurement_type), offer_type=VALUES(offer_type), offer_value=VALUES(offer_value), unit=VALUES(unit), stock_quantity=VALUES(stock_quantity), low_stock_threshold=VALUES(low_stock_threshold), is_featured=VALUES(is_featured), status=VALUES(status), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), search_keywords=VALUES(search_keywords);

-- ── Product images (reuse existing uploaded files) ────────────────────
INSERT IGNORE INTO product_images (id, product_id, image_path, alt_text, sort_order, is_primary) VALUES
  (100,  6, '/assets/uploads/products/46ef3a6677e5432cd42484a62afb88ed.png', 'Sunflower smile bouquet', 1, 1),
  (101,  6, '/assets/uploads/products/6f79887db14610e33be72e208d4c0134.png', 'Sunflower bouquet detail', 2, 0),
  (102,  7, '/assets/uploads/products/208224bbb4473b7cab02ec9815c0918b.png', 'Red velvet rose bouquet', 1, 1),
  (103,  7, '/assets/uploads/products/57f430aca879107cc72caf5c428f8664.png', 'Red roses close-up', 2, 0),
  (104,  8, '/assets/uploads/products/8b03be2ceed1214d38f6df59609c5773.png', 'Tulip and baby breath bunch', 1, 1),
  (105,  8, '/assets/uploads/products/b437ed6a1fea72f7e90bef26d65b3086.png', 'Pastel tulip detail', 2, 0),
  (106,  9, '/assets/uploads/products/75a10e81a11756d293c0edcb32e23780.png', 'Chocolate lovers hamper', 1, 1),
  (107,  9, '/assets/uploads/products/b2cd39a2d6124282fe4881b294e5b592.png', 'Hamper contents', 2, 0),
  (108, 10, '/assets/uploads/products/42537d834727665cf6693ccc567bfdb8.png', 'Spa and serenity hamper', 1, 1),
  (109, 11, '/assets/uploads/products/4306153b7a3aa2569ee87dd5fd6ca09f.png', 'Birthday bash hamper', 1, 1),
  (110, 11, '/assets/uploads/products/7bdaeb29dd02924957a6d707335b3913.png', 'Birthday hamper treats', 2, 0),
  (111, 12, '/assets/uploads/products/1e6a4353627593a79329817d77ce91ca.png', 'Belgian chocolate box', 1, 1),
  (112, 12, '/assets/uploads/products/501abe1ce41e1bd6e4ae2eb5a6a647bc.png', 'Chocolate assortment', 2, 0),
  (113, 13, '/assets/uploads/products/2c0c24f03a4fcd3db32d4c075a164a95.png', 'Assorted macaron set', 1, 1),
  (114, 14, '/assets/uploads/products/7a80ae0588384d0c6674d30af24ec584.png', 'Honey and nut delight jar', 1, 1),
  (115, 15, '/assets/uploads/products/9e5510aa7e9f5a2297bce026674abc7f.png', 'Giant cuddly teddy bear', 1, 1),
  (116, 15, '/assets/uploads/products/1eed11e73a12ab59741849c355876084.png', 'Teddy bear detail', 2, 0),
  (117, 16, '/assets/uploads/products/93747d1659e3089c4c37be2a3ed3ec30.png', 'Mini bunny plush pair', 1, 1),
  (118, 16, '/assets/uploads/products/da9a4f5051cd145e96fdc154c9a7b998.png', 'Bunny pair detail', 2, 0),
  (119, 17, '/assets/uploads/products/d4a50e1b7769d14a3bccfd31c91b8d61.png', 'Rose gold jewellery box', 1, 1),
  (120, 18, '/assets/uploads/products/abc5223543b451eb08496946b549f7a4.png', 'Anniversary keepsake box', 1, 1),
  (121, 18, '/assets/uploads/products/d705f5373ea1809f4264fd7e69466404.png', 'Keepsake box detail', 2, 0),
  (122, 19, '/assets/uploads/products/3c666ac96f1f58e0c677d5a0ed0a25e7.png', 'Aurora panda night lamp', 1, 1),
  (123, 20, '/assets/uploads/products/2c0c24f03a4fcd3db32d4c075a164a95.png', 'Holiday surprise box (draft)', 1, 1);

-- ── Colour variants ───────────────────────────────────────────────────
INSERT IGNORE INTO product_variants (id, product_id, name, sku, color_name, color_hex, price_adjustment, stock_quantity, status) VALUES
  (10,  7, 'Red Velvet Rose (Red)',   'GV-ROSE-VELVET-RED',   'Red',    '#DC2626',  400.00, 8,  'active'),
  (11,  7, 'Red Velvet Rose (Pink)',  'GV-ROSE-VELVET-PINK',  'Pink',   '#F472B6',  200.00, 4,  'active'),
  (12,  7, 'Red Velvet Rose (White)', 'GV-ROSE-VELVET-WHITE', 'White',  '#F1F5F9',    0.00, 3,  'active'),
  (13, 12, 'Belgian Choc (Milk)',     'GV-BELGIAN-CHOC-MILK', 'Milk',   '#A9714C',    0.00, 15, 'active'),
  (14, 12, 'Belgian Choc (Dark)',     'GV-BELGIAN-CHOC-DARK', 'Dark',   '#3F2A1F',  100.00, 10, 'active'),
  (15, 15, 'Teddy (Brown)',           'GV-TEDDY-GIANT-BRN',   'Brown',  '#92400E',    0.00, 4,  'active'),
  (16, 15, 'Teddy (White)',           'GV-TEDDY-GIANT-WHT',   'White',  '#F1F5F9',  300.00, 2,  'active'),
  (17, 15, 'Teddy (Grey)',            'GV-TEDDY-GIANT-GRY',   'Grey',   '#6B7280',  200.00, 1,  'active'),
  (18, 19, 'Panda Lamp (White)',      'GV-PANDA-AURORA-WHT',  'White',  '#F8FAFC',    0.00, 7,  'active'),
  (19, 19, 'Panda Lamp (Grey)',       'GV-PANDA-AURORA-GRY',  'Grey',   '#9CA3AF',  150.00, 3,  'active');

-- ── Product ↔ category mapping ────────────────────────────────────────
INSERT IGNORE INTO product_categories (product_id, category_id) VALUES
  (6, 2), (7, 2), (8, 2),
  (9, 3), (9, 4), (10, 3), (11, 3), (11, 6),
  (12, 4), (13, 4), (14, 4),
  (15, 5), (16, 5),
  (17, 6), (18, 6), (20, 6),
  (19, 7), (21, 2);

-- ── Green & Yellow Fresh Bouquet (colour variants demo) ───────────────
INSERT INTO products (id, sku, name, slug, short_description, description, base_price, sale_price, cost_price, procurement_type, offer_type, offer_value, unit, stock_quantity, low_stock_threshold, is_featured, status, meta_title, meta_description, search_keywords) VALUES
  (21, 'GV-GREEN-YELLOW-BQ', 'Green & Yellow Fresh Bouquet', 'green-yellow-fresh-bouquet',
   'A cheerful bouquet of green and yellow blooms.',
   'Bright yellow flowers paired with fresh green foliage — pick your preferred colour at checkout. Hand-tied and ready for delivery across Sri Lanka.',
   6000.00, NULL, 3400.00, 'purchased', 'none', NULL, 'bouquet', 20, 5, 1, 'active',
   'Green & Yellow Fresh Bouquet | GiftVibe',
   'Fresh green and yellow bouquet delivered across Sri Lanka.',
   'yellow bouquet, green bouquet, green and yellow flowers, mixed bouquet, fresh flowers colombo')
ON DUPLICATE KEY UPDATE sku=VALUES(sku), name=VALUES(name), slug=VALUES(slug), short_description=VALUES(short_description), description=VALUES(description), base_price=VALUES(base_price), cost_price=VALUES(cost_price), procurement_type=VALUES(procurement_type), unit=VALUES(unit), stock_quantity=VALUES(stock_quantity), low_stock_threshold=VALUES(low_stock_threshold), is_featured=VALUES(is_featured), status=VALUES(status), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), search_keywords=VALUES(search_keywords);

INSERT IGNORE INTO product_variants (id, product_id, name, sku, color_name, color_hex, image_path, price_adjustment, stock_quantity, status) VALUES
  (20, 21, 'Green & Yellow Bouquet (Green)',  'GV-GREEN-YELLOW-BQ-GREEN',  'Green',  '#16A34A', '/assets/uploads/products/8b03be2ceed1214d38f6df59609c5773.png', 0.00, 12, 'active'),
  (21, 21, 'Green & Yellow Bouquet (Yellow)', 'GV-GREEN-YELLOW-BQ-YELLOW', 'Yellow', '#FACC15', '/assets/uploads/products/46ef3a6677e5432cd42484a62afb88ed.png', 0.00, 8,  'active');

INSERT IGNORE INTO product_images (id, product_id, image_path, alt_text, sort_order, is_primary) VALUES
  (130, 21, '/assets/uploads/products/46ef3a6677e5432cd42484a62afb88ed.png', 'Green and yellow fresh bouquet', 1, 1),
  (131, 21, '/assets/uploads/products/6f79887db14610e33be72e208d4c0134.png', 'Green and yellow bouquet detail', 2, 0);

INSERT IGNORE INTO product_categories (product_id, category_id) VALUES (21, 2);

-- ── Sample gift combos ────────────────────────────────────────────────
INSERT INTO combos (id, name, slug, description, price, other_cost, status, meta_title, meta_description, search_keywords) VALUES
  (1, 'Birthday Surprise Combo', 'birthday-surprise-combo', 'A festive hamper, gourmet chocolates and a cuddly teddy — everything for a wonderful birthday surprise.', 15900.00, 0, 'active', 'Birthday Surprise Combo | GiftVibe', 'A ready-to-gift birthday surprise combo delivered across Sri Lanka.', 'birthday combo, birthday surprise, birthday hamper combo'),
  (2, 'Love & Roses Combo', 'love-roses-combo', 'Fresh red velvet roses paired with a box of Belgian chocolates for someone special.', 10900.00, 0, 'active', 'Love & Roses Combo | GiftVibe', 'Roses and chocolate combo for anniversaries and Valentine''s Day.', 'roses combo, chocolate roses combo, anniversary combo, valentine combo'),
  (3, 'Grand Celebration Combo', 'grand-celebration-combo', 'A spa hamper, a giant teddy and a rose gold jewellery box for unforgettable celebrations.', 22900.00, 0, 'active', 'Grand Celebration Combo | GiftVibe', 'A luxurious grand celebration combo.', 'grand combo, luxury combo, spa teddy combo'),
  (4, 'Sweet Moments Combo', 'sweet-moments-combo', 'Macarons, honey-nut delight and Belgian chocolates for the sweetest moments.', 8900.00, 0, 'active', 'Sweet Moments Combo | GiftVibe', 'A sweet treats combo of macarons, honey and chocolate.', 'sweet combo, chocolate macaron combo, dessert combo'),
  (5, 'Fresh Blooms Combo', 'fresh-blooms-combo', 'A sunflower smile bouquet and a tulip bunch for a bright, cheerful delivery.', 8900.00, 0, 'active', 'Fresh Blooms Combo | GiftVibe', 'A fresh flowers combo of sunflowers and tulips.', 'flowers combo, bouquet combo, fresh flowers combo')
ON DUPLICATE KEY UPDATE name=VALUES(name), description=VALUES(description), price=VALUES(price), status=VALUES(status), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description), search_keywords=VALUES(search_keywords);

INSERT IGNORE INTO combo_images (id, combo_id, image_path, alt_text, sort_order, is_primary) VALUES
  (1, 1, '/assets/uploads/products/7bdaeb29dd02924957a6d707335b3913.png', 'Birthday surprise combo', 1, 1),
  (2, 2, '/assets/uploads/products/208224bbb4473b7cab02ec9815c0918b.png', 'Love and roses combo', 1, 1),
  (3, 3, '/assets/uploads/products/42537d834727665cf6693ccc567bfdb8.png', 'Grand celebration combo', 1, 1),
  (4, 4, '/assets/uploads/products/2c0c24f03a4fcd3db32d4c075a164a95.png', 'Sweet moments combo', 1, 1),
  (5, 5, '/assets/uploads/products/46ef3a6677e5432cd42484a62afb88ed.png', 'Fresh blooms combo', 1, 1);

INSERT IGNORE INTO combo_products (combo_id, product_id, sort_order) VALUES
  (1, 11, 1), (1, 9, 2), (1, 15, 3),
  (2, 7, 1), (2, 12, 2),
  (3, 10, 1), (3, 15, 2), (3, 17, 3),
  (4, 13, 1), (4, 14, 2), (4, 12, 3),
  (5, 6, 1), (5, 8, 2);

SET FOREIGN_KEY_CHECKS = 1;
