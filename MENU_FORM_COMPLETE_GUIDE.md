# 📋 Complete Menu Form Fields - Full Documentation

## 🔗 API Endpoint for Form Data
```
GET /api/restaurants/{restaurant_id}/menu/form-data
```

---

## 📊 Complete Response Structure

```json
{
    "success": true,
    "message": "✅ Form data loaded successfully",
    "data": {
        "restaurant": {
            "id": 1,
            "name": "Restaurant Name",
            "legal_name": "Legal Name"
        },
        "categories": [...],
        "second_flavors": [...],
        "addons": [...],
        "form_options": {
            "currencies": [...],
            "status_options": [...],
            "availability_options": [...],
            "spice_levels": [...],
            "dietary_flags": [...],
            "tags": [...],
            "preparation_time_options": [...],
            "calorie_ranges": [...]
        },
        "fields_info": {
            "required_fields": [...],
            "optional_fields": [...],
            "image_upload": {...}
        }
    }
}
```

---

## 📝 ALL FORM FIELDS (Create/Update Menu)

### ✅ **Required Fields**

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `name` | string (max:255) | Menu item name | "Chicken Biryani" |
| `price` | decimal | Item price | 12.99 |
| `currency` | string | Currency code | "GBP", "USD", "EUR", "PKR" |
| `category_id` | integer | Menu category ID | 2 |

---

### 📝 **Optional Fields**

| Field | Type | Description | Example |
|-------|------|-------------|---------|
| `description` | text | Detailed description | "Aromatic basmati rice..." |
| `ingredients` | text | Ingredients list | "Chicken, Rice, Spices" |
| `vat_price` | decimal | VAT/Tax amount | 2.14 |
| `second_flavor_id` | integer | Second flavor ID | 5 |
| `status` | string | Menu status | "active" or "inactive" |
| `is_available` | boolean | Availability | true or false |
| `spice_level` | integer (0-5) | Spice intensity | 3 |
| `preparation_time` | integer (0-300) | Time in minutes | 25 |
| `calories` | integer (0-5000) | Calorie count | 650 |
| `tags` | string/array | Tags (comma-separated) | "Popular,Spicy,New" |
| `allergen` | string | Allergen info | "Contains Dairy, Nuts" |
| `dietary_flags` | string/array | Dietary info | "Halal,Non-Vegetarian" |
| `cold_drinks_addons` | array | Addon IDs | [14, 16, 20] |
| `images` | array | Image files (max 5) | [file1.jpg, file2.jpg] |

---

## 🎨 Form Options Details

### 1. **Categories** (From Database)
```json
"categories": [
    {
        "id": 1,
        "name": "Pizza",
        "description": "Italian pizzas",
        "image": "menu-categories/image.jpg",
        "image_url": "https://full-url"
    }
]
```

### 2. **Second Flavors** (From Database) ⭐
```json
"second_flavors": [
    {
        "id": 1,
        "name": "Extra Cheese",
        "image": "second-flavors/image.jpg",
        "image_url": "https://full-url"
    }
]
```

### 3. **Addons** (Restaurant Specific)
```json
"addons": [
    {
        "id": 14,
        "name": "Cold Drink",
        "price": 2.50,
        "description": "Refreshing beverages",
        "image": "restaurant-addons/image.jpg",
        "image_url": "https://full-url"
    }
]
```

### 4. **Currencies** (4 Options)
```json
"currencies": [
    {"value": "GBP", "label": "GBP (£)"},
    {"value": "USD", "label": "USD ($)"},
    {"value": "EUR", "label": "EUR (€)"},
    {"value": "PKR", "label": "PKR (₨)"}
]
```

### 5. **Status Options** (2 Options)
```json
"status_options": [
    {"value": "active", "label": "Active ✓"},
    {"value": "inactive", "label": "Inactive ✗"}
]
```

### 6. **Availability Options** (2 Options)
```json
"availability_options": [
    {"value": true, "label": "Available ✓"},
    {"value": false, "label": "Not Available ✗"}
]
```

### 7. **Spice Levels** (6 Options: 0-5)
```json
"spice_levels": [
    {"value": 0, "label": "No Spice"},
    {"value": 1, "label": "Mild (1⭐)"},
    {"value": 2, "label": "Medium (2⭐)"},
    {"value": 3, "label": "Hot (3⭐)"},
    {"value": 4, "label": "Very Hot (4⭐)"},
    {"value": 5, "label": "Extreme (5⭐)"}
]
```

### 8. **Dietary Flags** (11 Options) 🥗
```json
"dietary_flags": [
    {"value": "Vegetarian", "label": "🥗 Vegetarian"},
    {"value": "Vegan", "label": "🌱 Vegan"},
    {"value": "Halal", "label": "☪️ Halal"},
    {"value": "Kosher", "label": "✡️ Kosher"},
    {"value": "Gluten-Free", "label": "🌾 Gluten-Free"},
    {"value": "Dairy-Free", "label": "🥛 Dairy-Free"},
    {"value": "Nut-Free", "label": "🥜 Nut-Free"},
    {"value": "Non-Vegetarian", "label": "🍖 Non-Vegetarian"},
    {"value": "Contains Gluten", "label": "Contains Gluten"},
    {"value": "Contains Dairy", "label": "Contains Dairy"},
    {"value": "Contains Nuts", "label": "Contains Nuts"}
]
```

### 9. **Tags** (11 Options) 🏷️
```json
"tags": [
    {"value": "Popular", "label": "⭐ Popular"},
    {"value": "Premium", "label": "👑 Premium"},
    {"value": "Spicy", "label": "🌶️ Spicy"},
    {"value": "Healthy", "label": "💚 Healthy"},
    {"value": "Kids Favorite", "label": "👶 Kids Favorite"},
    {"value": "New", "label": "🆕 New"},
    {"value": "Bestseller", "label": "🔥 Bestseller"},
    {"value": "Chef Special", "label": "👨‍🍳 Chef Special"},
    {"value": "Authentic", "label": "✨ Authentic"},
    {"value": "Budget", "label": "💰 Budget"},
    {"value": "Organic", "label": "🌿 Organic"}
]
```

### 10. **Preparation Time** (7 Options) ⏱️
```json
"preparation_time_options": [
    {"value": 10, "label": "10 minutes"},
    {"value": 15, "label": "15 minutes"},
    {"value": 20, "label": "20 minutes"},
    {"value": 25, "label": "25 minutes"},
    {"value": 30, "label": "30 minutes"},
    {"value": 45, "label": "45 minutes"},
    {"value": 60, "label": "1 hour"}
]
```

### 11. **Calorie Ranges** (4 Options) 🔥
```json
"calorie_ranges": [
    {"value": "0-200", "label": "Low (0-200 cal)"},
    {"value": "200-400", "label": "Medium (200-400 cal)"},
    {"value": "400-600", "label": "High (400-600 cal)"},
    {"value": "600+", "label": "Very High (600+ cal)"}
]
```

---

## 📸 Image Upload Specifications

```json
"image_upload": {
    "max_images": 5,
    "max_size": "2MB per image",
    "formats": ["jpeg", "png", "jpg", "gif"]
}
```

---

## 🧪 Example 1: Create Menu (Minimum Fields)

```json
POST /api/menus/add

{
    "restaurant_id": 1,
    "name": "Margherita Pizza",
    "price": 8.99,
    "currency": "GBP",
    "category_id": 2
}
```

---

## 🧪 Example 2: Create Menu (ALL Fields)

```json
POST /api/menus/add

{
    "restaurant_id": 1,
    "name": "Chicken Biryani Deluxe",
    "description": "Aromatic basmati rice cooked with tender chicken pieces, premium spices, and saffron. Served with raita and pickle.",
    "ingredients": "Chicken, Basmati Rice, Onions, Ginger, Garlic, Cinnamon, Cardamom, Cloves, Bay Leaves, Saffron, Yogurt, Mint Leaves, Coriander",
    "price": 12.99,
    "vat_price": 2.14,
    "currency": "GBP",
    "category_id": 2,
    "second_flavor_id": 5,
    "status": "active",
    "is_available": true,
    "spice_level": 3,
    "preparation_time": 25,
    "calories": 650,
    "tags": "Popular,Spicy,Authentic,Premium",
    "allergen": "Contains Dairy, Nuts, Gluten",
    "dietary_flags": "Halal,Non-Vegetarian",
    "cold_drinks_addons": [14, 16, 20]
}
```

**Note:** For images, use `multipart/form-data` and add image files separately.

---

## 🔄 Example 3: Update Menu

```json
POST /api/menus/update

{
    "id": 86,
    "name": "Updated Menu Name",
    "price": 15.99,
    "vat_price": 2.64,
    "spice_level": 4,
    "is_available": true,
    "cold_drinks_addons": [14, 16],
    "tags": "Popular,New,Bestseller"
}
```

**Response:**
```json
{
    "success": true,
    "message": "🎉 Menu updated successfully! Your changes have been saved."
}
```

---

## 📋 Field Validation Rules

| Field | Validation | Notes |
|-------|-----------|-------|
| `name` | required, string, max:255 | - |
| `price` | required, numeric, min:0 | Must be positive |
| `vat_price` | nullable, numeric, min:0 | - |
| `currency` | required, in:GBP,USD,EUR,PKR | - |
| `category_id` | required, exists in menu_categories | Must be valid category |
| `second_flavor_id` | nullable, exists in second_flavors | Optional |
| `status` | nullable, in:active,inactive | Default: active |
| `is_available` | boolean | Default: true |
| `spice_level` | nullable, integer, 0-5 | - |
| `preparation_time` | nullable, integer, 0-300 | Minutes |
| `calories` | nullable, integer, 0-5000 | - |
| `tags` | nullable, string | Comma-separated |
| `dietary_flags` | nullable, string | Comma-separated |
| `cold_drinks_addons` | nullable | Array or comma-separated |
| `images` | nullable, array, max:5 | Each max 2MB |
| `images.*` | image, jpeg,png,jpg,gif | - |

---

## 📊 Summary Table

| Category | Count | Items |
|----------|-------|-------|
| **Required Fields** | 4 | name, price, currency, category_id |
| **Optional Fields** | 14 | description, ingredients, vat_price, second_flavor_id, status, is_available, spice_level, preparation_time, calories, tags, allergen, dietary_flags, cold_drinks_addons, images |
| **Total Fields** | 18 | All menu fields |
| **Currencies** | 4 | GBP, USD, EUR, PKR |
| **Spice Levels** | 6 | 0 to 5 |
| **Dietary Flags** | 11 | Vegetarian, Vegan, Halal, etc. |
| **Tags** | 11 | Popular, Premium, Spicy, etc. |
| **Prep Times** | 7 | 10 min to 1 hour |
| **Calorie Ranges** | 4 | Low to Very High |

---

## 🎯 API Endpoints

| Method | Endpoint | Purpose |
|--------|----------|---------|
| `GET` | `/api/restaurants/{id}/menu/form-data` | Get form options |
| `POST` | `/api/menus/add` | Create menu |
| `POST` | `/api/menus/update` | Update menu |
| `POST` | `/api/menus/list` | List menus |
| `POST` | `/api/menus/view` | View single menu |
| `DELETE` | `/api/menus/delete` | Delete menu |

---

## ✅ Complete Features

✅ **Categories** - Dynamic from database  
✅ **Second Flavors** - Dynamic from database  
✅ **Addons** - Restaurant specific  
✅ **Currencies** - 4 major currencies  
✅ **Status** - Active/Inactive  
✅ **Availability** - Available/Not Available  
✅ **Spice Levels** - 0-5 with emojis  
✅ **Dietary Flags** - 11 options with emojis  
✅ **Tags** - 11 popular tags with emojis  
✅ **Prep Time** - 7 time options  
✅ **Calories** - 4 calorie ranges  
✅ **Images** - Max 5 images, 2MB each  
✅ **Validation** - Complete field validation  
✅ **Error Messages** - Beautiful emoji messages  

---

**🎉 Sare fields complete! Ab menu form fully functional hai!**

