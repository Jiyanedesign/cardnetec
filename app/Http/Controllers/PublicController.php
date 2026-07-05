<?php

namespace App\Http\Controllers;

use App\Models\BeforeAfterItem;
use App\Models\CarouselSlide;
use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $slides = CarouselSlide::where('is_active', true)->orderBy('order')->get();
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('order')
            ->take(6)
            ->get();
        $beforeAfters = BeforeAfterItem::where('is_active', true)->orderBy('order')->take(3)->get();
        $materials = Material::where('is_active', true)->orderBy('order')->take(4)->get();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->orderBy('order')->take(5)->get();

        return view('index', compact('slides', 'featuredProducts', 'beforeAfters', 'materials', 'categories'));
    }

    public function productos(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->has('categoria')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->categoria);
            });
        }

        $products = $query->orderBy('order')->get();
        $categories = Category::where('is_active', true)->whereNull('parent_id')->get();

        return view('productos', compact('products', 'categories'));
    }

    public function empresas()
    {
        return view('empresas');
    }

    public function cotizacion()
    {
        return view('cotizacion');
    }

    public function contacto()
    {
        return view('contacto');
    }

    public function nosotros()
    {
        return view('nosotros');
    }

    public function faq()
    {
        return view('faq');
    }

    public function personalizacion()
    {
        return view('personalizacion');
    }

    public function simulador(Request $request)
    {
        $selectedProductId = $request->get('producto_id');
        $selectedProduct = null;
        
        if ($selectedProductId) {
            $selectedProduct = Product::find($selectedProductId);
        }

        $simulableProducts = Product::where('is_active', true)->where('allows_simulation', true)->get();

        return view('simulador', compact('simulableProducts', 'selectedProduct'));
    }

    public function simuladorCarnets()
    {
        return view('simulador-carnets');
    }
}
