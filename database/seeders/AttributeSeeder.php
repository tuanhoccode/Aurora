<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo thuộc tính Màu sắc
        $colorAttribute = Attribute::create([
            'name' => 'Màu sắc',
            'is_variant' => true,
            'is_active' => true,
        ]);

        // Tạo các giá trị màu sắc
        $colors = ['Đỏ', 'Xanh', 'Trắng', 'Đen', 'Vàng', 'Xanh lá', 'Xanh dương', 'Cam', 'Tím', 'Nâu', 'Hồng', 'Xám'];
        foreach ($colors as $color) {
            AttributeValue::create([
                'attribute_id' => $colorAttribute->id,
                'value' => $color,
                'is_active' => true,
            ]);
        }

        // Tạo thuộc tính Kích thước
        $sizeAttribute = Attribute::create([
            'name' => 'Kích thước',
            'is_variant' => true,
            'is_active' => true,
        ]);

        // Tạo các giá trị kích thước
        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
        foreach ($sizes as $size) {
            AttributeValue::create([
                'attribute_id' => $sizeAttribute->id,
                'value' => $size,
                'is_active' => true,
            ]);
        }

        // Tạo thuộc tính Chất liệu
        $materialAttribute = Attribute::create([
            'name' => 'Chất liệu',
            'is_variant' => false,
            'is_active' => true,
        ]);

        // Tạo các giá trị chất liệu
        $materials = ['Cotton', 'Polyester', 'Denim', 'Silk', 'Wool', 'Linen'];
        foreach ($materials as $material) {
            AttributeValue::create([
                'attribute_id' => $materialAttribute->id,
                'value' => $material,
                'is_active' => true,
            ]);
        }
    }
} 