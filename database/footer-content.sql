INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('site.name', 'GiftVibe LK', 'general'),
('site.description', 'Thoughtful gifts, curated gift boxes, flowers and celebration combos delivered across Sri Lanka.', 'general'),
('site.contact_email', 'hello@giftvibelk.lk', 'contact'),
('site.contact_phone', '+94 77 123 4567', 'contact'),
('site.contact_address', 'Colombo, Sri Lanka', 'contact'),
('footer.quick_links', '[{"label":"Home","href":"/"},{"label":"Shop","href":"/shop"},{"label":"Services","href":"/services"},{"label":"Blog","href":"/blog"},{"label":"About Us","href":"/about"},{"label":"Contact","href":"/contact"}]', 'footer'),
('footer.products_links', '[{"label":"All Gifts","href":"/shop"},{"label":"Gift Combos","href":"/combos"},{"label":"Birthday Gifts","href":"/shop?category=birthday-gifts"},{"label":"Flowers","href":"/shop?category=flowers"},{"label":"Sweet Treats","href":"/shop?category=sweet-treats"}]', 'footer')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value), setting_group=VALUES(setting_group);
