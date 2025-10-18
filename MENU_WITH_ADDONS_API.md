# Menu with Addons API Documentation

## Overview
This API allows you to retrieve menu items with their associated addons in a single request.

---

## Endpoints

### 1. Get Menu Item with Addons
**POST** `/api/menus/with-addons`

**Authentication:** Required (Bearer Token)

**Request Body:**
```json
{
    "menu_id": 17
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Menu item with addons retrieved successfully",
    "data": {
        "id": 17,
        "restaurant_id": 17,
        "name": "Tikka Masala",
        "category_id": 2,
        "price": 10.99,
        "vat_price": 1.83,
        "currency": "GBP",
        "status": 1,
        "spice_level": 3,
        "preparation_time": 25,
        "calories": 65,
        "tags": "Premium, Spicy, Non-Vegetarian, Popular, Authentic, Halal",
        "description": "Aromatic basmati rice cooked with tender chicken pieces...",
        "ingredients": "Chicken, Basmati Rice, Onions, Ginger, Garlic...",
        "allergen": "Contains Dairy, Nuts, Gluten",
        "dietary_flags": "Halal, Non-Vegetarian, Contains Gluten",
        "is_available": 1,
        "image": "menu-items/image.jpg",
        "addons": [
            {
                "menu_id": 17,
                "addid": 101,
                "name": "Extra Raita",
                "price": 1.50,
                "currency": "GBP",
                "is_available": 1,
                "description": "Cool yogurt-based dip with cucumber and mint"
            },
            {
                "menu_id": 17,
                "addid": 102,
                "name": "Butter Naan",
                "price": 2.00,
                "currency": "GBP",
                "is_available": 1,
                "description": "Soft, buttery Indian bread baked in a tandoor"
            }
        ],
        "restaurant": {
            "id": 17,
            "business_name": "Spice Garden",
            "legal_name": "Spice Garden Ltd",
            "city": "London",
            "phone": "+44 20 1234 5678"
        }
    }
}
```

---

### 2. Get Restaurant Complete Details (with Menus & Addons)
**POST** `/api/restaurants/complete-details`

**Authentication:** Not Required (Public)

**Request Body:**
```json
{
    "restaurant_id": 17
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Restaurant details retrieved successfully",
    "data": {
        "restaurant": {
            "id": 17,
            "business_name": "Spice Garden",
            "legal_name": "Spice Garden Ltd",
            "email": "info@spicegarden.com",
            "phone": "+44 20 1234 5678",
            "address_line1": "123 High Street",
            "city": "London",
            "postcode": "SW1A 1AA",
            "opening_time": "11:00",
            "closing_time": "23:00",
            "min_order": 15.00,
            "status": "active",
            "cuisine_tags": "Indian, Pakistani, Halal",
            "delivery_zone": "5",
            "logo": "restaurant-logos/logo.jpg",
            "banner": "restaurant-banners/banner.jpg"
        },
        "menus": [
            {
                "id": 17,
                "restaurant_id": 17,
                "name": "Tikka Masala",
                "category_id": 2,
                "price": 10.99,
                "vat_price": 1.83,
                "currency": "GBP",
                "status": 1,
                "is_available": 1,
                "spice_level": 3,
                "preparation_time": 25,
                "calories": 65,
                "tags": "Premium, Spicy, Non-Vegetarian",
                "description": "Aromatic basmati rice...",
                "ingredients": "Chicken, Basmati Rice...",
                "allergen": "Contains Dairy, Nuts, Gluten",
                "dietary_flags": "Halal, Non-Vegetarian",
                "image": "menu-items/image.jpg",
                "addons": [
                    {
                        "menu_id": 17,
                        "addid": 101,
                        "name": "Extra Raita",
                        "price": 1.50,
                        "currency": "GBP",
                        "is_available": 1,
                        "description": "Cool yogurt-based dip"
                    }
                ]
            }
        ],
        "certificates": []
    }
}
```

---

## Error Responses

### Validation Error (200 OK):
```json
{
    "success": false,
    "message": "The menu id field is required."
}
```

### Menu Not Found (200 OK):
```json
{
    "success": false,
    "message": "Menu item not found"
}
```

### Server Error (500):
```json
{
    "success": false,
    "message": "Failed to retrieve menu item",
    "error": "Error message here"
}
```

---

## Usage Examples

### cURL - Get Menu with Addons:
```bash
curl -X POST https://api.todaynews.agency/api/menus/with-addons \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "menu_id": 17
  }'
```

### cURL - Get Restaurant Complete Details:
```bash
curl -X POST https://api.todaynews.agency/api/restaurants/complete-details \
  -H "Content-Type: application/json" \
  -d '{
    "restaurant_id": 17
  }'
```

---

## How to Link Menu and Addons

### Method 1: Via Database (Recommended)
Insert into `menu_addon` pivot table:
```sql
INSERT INTO menu_addon (menu_id, restaurant_addon_id, created_at, updated_at)
VALUES (17, 101, NOW(), NOW());
```

### Method 2: Via Laravel Tinker
```php
$menu = \App\Models\Menu::find(17);
$menu->addons()->attach([101, 102, 103, 104]);
```

### Method 3: Via API (Future Enhancement)
Create API endpoint to manage menu-addon relationships:
```
POST /api/menus/{menu_id}/addons/attach
POST /api/menus/{menu_id}/addons/detach
```

---

## Features

✅ **Single API Call** - Get menu with all addons in one request
✅ **Active Addons Only** - Only shows active/available addons
✅ **Complete Details** - All menu fields included
✅ **Restaurant Info** - Optional restaurant details
✅ **Consistent Format** - Same structure as your example
✅ **Error Handling** - Clean 200 OK responses

---

## Database Structure

### menu_addon (Pivot Table)
- `id` - Primary key
- `menu_id` - Foreign key to menus table
- `restaurant_addon_id` - Foreign key to restaurant_addons table
- `created_at` - Timestamp
- `updated_at` - Timestamp

### Relationships
- Menu belongsToMany RestaurantAddon
- RestaurantAddon belongsToMany Menu

---

## Next Steps

To populate menu-addon relationships:

1. **Via Tinker:**
```bash
php artisan tinker
$menu = \App\Models\Menu::find(17);
$menu->addons()->sync([101, 102, 103, 104]);
```

2. **Via SQL:**
```sql
INSERT INTO menu_addon (menu_id, restaurant_addon_id) VALUES
(17, 101),
(17, 102),
(17, 103),
(17, 104);
```

3. **Create Management API** (Future):
- API to attach/detach addons to menu items
- Bulk addon assignment
- Addon reordering

