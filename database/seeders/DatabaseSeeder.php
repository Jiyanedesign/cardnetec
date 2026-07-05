<?php

namespace Database\Seeders;

use App\Models\BeforeAfterItem;
use App\Models\CarouselSlide;
use App\Models\Category;
use App\Models\Material;
use App\Models\PersonalizationTechnique;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear Administrador Inicial
        User::updateOrCreate(
            ['email' => 'admin@cardnet.ec'],
            [
                'name' => 'CardNet Admin',
                'password' => Hash::make('CardNetSecure2026!'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Crear Categorías Base
        $catArticulos = Category::updateOrCreate(
            ['slug' => 'articulos-personalizados'],
            [
                'name' => 'Artículos personalizados',
                'description' => 'Termos, agendas y regalos grabados con láser.',
                'is_active' => true,
                'is_featured' => true,
                'order' => 1,
            ]
        );

        $catIdentificacion = Category::updateOrCreate(
            ['slug' => 'identificacion-corporativa'],
            [
                'name' => 'Identificación corporativa',
                'description' => 'Carnets PVC, credenciales y porta credenciales.',
                'is_active' => true,
                'is_featured' => true,
                'order' => 2,
            ]
        );

        $catReconocimientos = Category::updateOrCreate(
            ['slug' => 'reconocimientos'],
            [
                'name' => 'Reconocimientos',
                'description' => 'Placas y trofeos personalizados.',
                'is_active' => true,
                'is_featured' => true,
                'order' => 3,
            ]
        );

        // 3. Crear Materiales Base
        $matAcero = Material::updateOrCreate(['slug' => 'acero-inoxidable'], ['name' => 'Acero inoxidable', 'is_active' => true]);
        $matMadera = Material::updateOrCreate(['slug' => 'madera'], ['name' => 'Madera', 'is_active' => true]);
        $matAcrilico = Material::updateOrCreate(['slug' => 'acrilico'], ['name' => 'Acrílico', 'is_active' => true]);
        $matCuero = Material::updateOrCreate(['slug' => 'cuero-pu'], ['name' => 'Cuero / PU', 'is_active' => true]);

        // 4. Crear Técnicas de Personalización
        $tecLaser = PersonalizationTechnique::updateOrCreate(['slug' => 'grabado-laser'], ['name' => 'Grabado láser', 'is_active' => true, 'is_primary' => true]);
        $tecUV = PersonalizationTechnique::updateOrCreate(['slug' => 'impresion-uv'], ['name' => 'Impresión UV', 'is_active' => true]);
        $tecRelieve = PersonalizationTechnique::updateOrCreate(['slug' => 'bajo-relieve'], ['name' => 'Bajo relieve o cuño seco', 'is_active' => true]);

        // 5. Crear Productos Base
        $prodTermos = Product::updateOrCreate(
            ['slug' => 'termos-grabados'],
            [
                'name' => 'Termos grabados',
                'description_short' => 'Termos de acero inoxidable grabados con acabado limpio, sobrio y resistente al uso diario.',
                'category_id' => $catArticulos->id,
                'allows_simulation' => true,
                'is_featured' => true,
                'is_active' => true,
                'order' => 1,
                'cta_text' => 'Quiero este acabado',
            ]
        );
        $prodTermos->materials()->sync([$matAcero->id]);
        $prodTermos->techniques()->sync([$tecLaser->id]);

        $prodAgendas = Product::updateOrCreate(
            ['slug' => 'agendas-personalizadas'],
            [
                'name' => 'Agendas personalizadas',
                'description_short' => 'Libretas con cubiertas de tacto cuero listas para grabados de gran textura y sobriedad.',
                'category_id' => $catArticulos->id,
                'allows_simulation' => true,
                'is_featured' => true,
                'is_active' => true,
                'order' => 2,
                'cta_text' => 'Solicitar este producto',
            ]
        );
        $prodAgendas->materials()->sync([$matCuero->id]);
        $prodAgendas->techniques()->sync([$tecRelieve->id, $tecLaser->id]);

        $prodPlacas = Product::updateOrCreate(
            ['slug' => 'placas-reconocimientos'],
            [
                'name' => 'Placas y reconocimientos',
                'description_short' => 'Placas conmemorativas de madera noble, vidrio pulido y acrílico con cortes limpios.',
                'category_id' => $catReconocimientos->id,
                'allows_simulation' => true,
                'is_featured' => true,
                'is_active' => true,
                'order' => 3,
                'cta_text' => 'Cotizar una idea',
            ]
        );
        $prodPlacas->materials()->sync([$matAcrilico->id, $matMadera->id]);
        $prodPlacas->techniques()->sync([$tecLaser->id]);

        // 6. Crear Slides del Carrusel
        CarouselSlide::updateOrCreate(
            ['title' => 'Productos personalizados que hacen visible el valor de tu marca'],
            [
                'subtitle' => 'Grabado láser, termos, agendas, placas y kits corporativos con acabados limpios y duraderos.',
                'cta_text' => 'Ver productos destacados',
                'cta_url' => '#destacados',
                'order' => 1,
                'is_active' => true,
            ]
        );

        CarouselSlide::updateOrCreate(
            ['title' => 'Termos grabados para empresas'],
            [
                'subtitle' => 'Acabado láser sobre acero, sobrio y resistente al uso diario.',
                'cta_text' => 'Quiero algo similar',
                'cta_url' => 'cotizacion.html',
                'order' => 2,
                'is_active' => true,
            ]
        );

        CarouselSlide::updateOrCreate(
            ['title' => 'Kits corporativos con mejor presentación'],
            [
                'subtitle' => 'Piezas seleccionadas para representar tu marca desde el primer contacto.',
                'cta_text' => 'Cotizar una idea',
                'cta_url' => 'cotizacion.html',
                'order' => 3,
                'is_active' => true,
            ]
        );

        // 7. Antes y Después
        BeforeAfterItem::updateOrCreate(
            ['title' => 'Termos de acero inoxidable'],
            [
                'technique' => 'Grabado láser de fibra',
                'material' => 'Acero inoxidable',
                'order' => 1,
                'is_active' => true,
            ]
        );

        BeforeAfterItem::updateOrCreate(
            ['title' => 'Agendas ejecutivas'],
            [
                'technique' => 'Bajo relieve',
                'material' => 'Cuero PU',
                'order' => 2,
                'is_active' => true,
            ]
        );

        BeforeAfterItem::updateOrCreate(
            ['title' => 'Cajas de madera corporativas'],
            [
                'technique' => 'Grabado láser CO2',
                'material' => 'Madera Pino',
                'order' => 3,
                'is_active' => true,
            ]
        );

        // 8. Site Settings
        SiteSetting::setValue('whatsapp_number', '593900000000', 'contact');
        SiteSetting::setValue('contact_email', 'info@cardnet.ec', 'contact');
        SiteSetting::setValue('site_descriptor', 'Grabado láser y personalización corporativa', 'general');
    }
}
