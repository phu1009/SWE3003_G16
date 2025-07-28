/* =========================
   1) ROLES
   ========================= */
INSERT INTO roles (role_name) VALUES
  ('customer'),
  ('pharmacist'),
  ('cashier'),
  ('manager'),
  ('admin');

/* =========================
   2) BRANCHES  (3 sample stores in HCMC)
   ========================= */
INSERT INTO branches (name, address, city, phone) VALUES
  ('Long Chau – District 1',  '123 Nguyen Hue St, District 1', 'Ho Chi Minh', '028‑1234‑1111'),
  ('Long Chau – District 3',  '456 Vo Van Tan St, District 3', 'Ho Chi Minh', '028‑1234‑3333'),
  ('Long Chau – Thu Duc',     '789 Vo Van Ngan St, Thu Duc',  'Ho Chi Minh', '028‑1234‑7777');

/* =========================
   3) PRODUCTS
   - Mix of OTC and Rx‑only items
   - unit_price in VND (thousand‑dong)
   ========================= */
INSERT INTO products (sku, product_name, description, unit_price, is_rx_only) VALUES
  ('OTC‑PARA‑500',  'Paracetamol 500 mg (10 tablets)',      'Pain & fever relief',                       15000,  FALSE),
  ('OTC‑VITC‑1000', 'Vitamin C 1000 mg (10 effervescent)',  'Immune support supplement',                 28000,  FALSE),
  ('OTC‑SALINE‑T',  '0.9% Saline Nasal Spray 100 ml',       'Nasal irrigation solution',                 30000,  FALSE),

  ('RX‑AMOX‑500',   'Amoxicillin 500 mg (20 capsules)',     'Broad‑spectrum antibiotic',                 45000,   TRUE),
  ('RX‑GLIB‑5',     'Glibenclamide 5 mg (30 tablets)',      'Oral antidiabetic agent',                   52000,   TRUE),
  ('RX‑ATOR‑20',    'Atorvastatin 20 mg (30 tablets)',      'Cholesterol‑lowering statin',               78000,   TRUE);

/* =========================
   4) INITIAL INVENTORY
   – Give each branch some stock
   ========================= */
INSERT INTO inventory (branch_id, product_id, quantity) VALUES
  -- District 1
  (1, 1, 500),  -- Paracetamol
  (1, 2, 300),
  (1, 3, 200),
  (1, 4, 150),
  (1, 5, 100),
  (1, 6, 120),

  -- District 3
  (2, 1, 400),
  (2, 2, 250),
  (2, 3, 180),
  (2, 4, 100),
  (2, 5, 90),
  (2, 6, 110),

  -- Thu Duc
  (3, 1, 350),
  (3, 2, 200),
  (3, 3, 150),
  (3, 4,  80),
  (3, 5,  70),
  (3, 6,  90);

INSERT INTO products
  (sku, product_name, description, unit_price, is_rx_only,
   brand_id, type_id, origin_country, image_path, rx_type)
VALUES
  ('ALFAC-100', 'Alfacalcidol 0.25 µg Capsule',
   'Vitamin D analogue, used for osteoporosis.', 5.20, 1,
   (SELECT brand_id FROM brands WHERE brand_name='DHG Pharma'),
   (SELECT type_id  FROM product_types WHERE type_name='Drug'),
   (SELECT country_id FROM countries WHERE country_name='Vietnam'),
   'images/products/alfacalcidol.jpg', 'RX'),

  ('BAYER-C500', 'Redoxon Vitamin C 500 mg',
   'Immune support supplement, orange flavour.', 7.90, 0,
   (SELECT brand_id FROM brands WHERE brand_name='Bayer'),
   (SELECT type_id  FROM product_types WHERE type_name='Supplement'),
   (SELECT country_id FROM countries WHERE country_name='Germany'),
   'images/products/redoxon.jpg', 'OTC');

/* Tag Redoxon for Adults & Children */
INSERT INTO product_patient_groups (product_id, group_id)
SELECT p.product_id, g.group_id
  FROM products p, patient_groups g
 WHERE p.sku='BAYER-C500' AND g.group_name IN ('Adult','Children');

