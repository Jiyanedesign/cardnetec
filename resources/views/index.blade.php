@extends('layouts.app')

@section('title', 'CardNet.ec | Grabado Láser y Personalización Corporativa')

@section('content')
    <!-- Hero con Carrusel Dinámico -->
    <section class="hero-block reveal-on-scroll">
        <div class="container hero-carousel-wrapper">
            <div class="hero-carousel">
                <div class="carousel-track">
                    @forelse($slides as $slide)
                        <div class="carousel-slide">
                            <div class="carousel-image-container">
                                <div class="image-placeholder theme-gray" style="height: 100%; border-radius: 0;">
                                    <div class="image-placeholder-inner" style="background-color: var(--surface-light);">
                                        <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.2">
                                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        </svg>
                                        <span class="image-placeholder-text">{{ $slide->title }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-slide-content">
                                <h2 class="carousel-slide-title">{{ $slide->title }}</h2>
                                @if($slide->subtitle)
                                    <p class="carousel-slide-subtitle">{{ $slide->subtitle }}</p>
                                @endif
                                @if($slide->cta_text)
                                    <a href="{{ $slide->cta_url ?? '#' }}" class="btn btn-primary">{{ $slide->cta_text }}</a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <!-- Fallback Slide si la base de datos está vacía -->
                        <div class="carousel-slide">
                            <div class="carousel-image-container">
                                <div class="image-placeholder theme-gray" style="height: 100%; border-radius: 0;">
                                    <div class="image-placeholder-inner" style="background-color: var(--surface-light);">
                                        <span class="image-placeholder-text">Cargando catálogo CardNet.ec...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-slide-content">
                                <h2 class="carousel-slide-title">Productos personalizados que hacen visible el valor de tu marca</h2>
                                <p class="carousel-slide-subtitle">Termos, agendas y reconocimientos de alta precisión y calidad.</p>
                                <a href="#destacados" class="btn btn-primary">Ver productos destacados</a>
                            </div>
                        </div>
                    @endforelse
                </div>

                <button class="carousel-control prev" aria-label="Anterior">←</button>
                <button class="carousel-control next" aria-label="Siguiente">→</button>
                <div class="carousel-indicators"></div>
            </div>
        </div>
    </section>

    <!-- Barra de Garantías Corporativas -->
    <section class="satisfaction-bar">
        <div class="container satisfaction-grid">
            <div class="satisfaction-item">
                <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <span>Acabado limpio sin tintas</span>
            </div>
            <div class="satisfaction-item">
                <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                <span>Prueba visual previa al marcaje</span>
            </div>
            <div class="satisfaction-item">
                <svg class="satisfaction-icon" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                <span>Soportes seleccionados</span>
            </div>
        </div>
    </section>

    <!-- Productos Destacados Dinámicos -->
    <section id="destacados" class="section-padding container reveal-on-scroll">
        <div class="section-header center">
            <span class="section-subtitle">Showroom Digital</span>
            <h2>Productos destacados</h2>
            <p>Una selección de artículos adaptados para grabados y marcajes de alta calidad.</p>
        </div>
        
        <div class="grid-3" style="margin-top: 2rem;">
            @foreach($featuredProducts as $product)
                <div class="product-card">
                    <div class="product-card-image-wrap">
                        <div class="image-placeholder theme-gray">
                            <div class="image-placeholder-inner">
                                <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                                <span class="image-placeholder-text">{{ $product->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="product-card-body">
                        <span class="product-card-price">
                            {{ $product->category->name }}
                            @if($product->allows_simulation)
                                · Simulable
                            @endif
                        </span>
                        <h3 class="product-card-title">{{ $product->name }}</h3>
                        <p class="product-card-desc">{{ $product->description_short }}</p>
                        @if($product->allows_simulation)
                            <a href="{{ route('simulador', ['producto_id' => $product->id]) }}" class="btn btn-secondary" style="margin-top: auto; padding: 0.5rem 1rem; font-size: 0.8rem;">Simular mi logo</a>
                        @else
                            <a href="{{ route('cotizacion', ['producto' => $product->slug]) }}" class="btn btn-secondary" style="margin-top: auto; padding: 0.5rem 1rem; font-size: 0.8rem;">{{ $product->cta_text ?? 'Quiero este acabado' }}</a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Antes y Después Dinámico -->
    @if($beforeAfters->isNotEmpty())
        <section id="antes-despues" class="section-padding container reveal-on-scroll">
            <div class="section-header center">
                <span class="section-subtitle">Evidencia de Taller</span>
                <h2>Antes y después del grabado</h2>
                <p>Compara el producto base limpio frente al resultado final con identidad de marca grabada.</p>
            </div>
            
            <div class="comparison-grid" style="margin-top: 2rem;">
                @foreach($beforeAfters as $item)
                    <div class="comparison-card">
                        <div class="comparison-views">
                            <div class="comparison-view">
                                <div class="comparison-label">Antes</div>
                                <span style="font-size: 0.8rem; color: var(--text-muted);">Liso sin marcar</span>
                            </div>
                            <div class="comparison-view after">
                                <div class="comparison-label after">Después</div>
                                <span style="font-weight: 600; color: var(--primary);">{{ $item->technique }}</span>
                            </div>
                        </div>
                        <div class="comparison-desc">{{ $item->title }} ({{ $item->material }})</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Materiales Dinámicos -->
    @if($materials->isNotEmpty())
        <section id="materiales" class="section-padding section-bg-light reveal-on-scroll">
            <div class="container">
                <div class="section-header center">
                    <span class="section-subtitle">Soportes Reales</span>
                    <h2>Materiales que trabajamos</h2>
                    <p>Seleccionamos materiales óptimos para lograr marcajes permanentes y definidos.</p>
                </div>
                
                <div class="materials-grid" style="margin-top: 2rem;">
                    @foreach($materials as $material)
                        <div class="material-card">
                            <h3 class="material-title">{{ $material->name }}</h3>
                            <p class="material-desc">{{ $material->description ?? 'Selección de calidad para marcas que cuidan su presentación.' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Especialidad Láser -->
    <section id="laser" class="section-padding container reveal-on-scroll">
        <div class="laser-section">
            <div class="laser-layout">
                <div class="laser-content">
                    <span class="section-subtitle" style="color: var(--primary); border-color: var(--primary);">Técnica Especializada</span>
                    <h2 style="color: var(--light); margin-bottom: 1.25rem;">Nuestra especialidad: grabado láser</h2>
                    <p style="margin-bottom: 2rem; font-size: 0.98rem; color: rgba(252,253,251,0.85);">El láser trabaja directamente sobre la superficie del material para lograr una marca limpia, precisa y duradera. Grabamos sobre metal, madera, cuero, acrílico y otros materiales para lograr piezas limpias, duraderas y elegantes.</p>
                    
                    <div class="laser-capabilities">
                        <div class="laser-cap-item">
                            <span>No se despega</span>
                            <h4>Marcado permanente</h4>
                        </div>
                        <div class="laser-cap-item">
                            <span>Detalles finos</span>
                            <h4>Sin tintas superficiales</h4>
                        </div>
                    </div>
                </div>

                <div class="laser-visual">
                    <div class="image-placeholder theme-gray" style="aspect-ratio: 1.15;">
                        <div class="image-placeholder-inner" style="background-color: var(--dark-alt); border-color: rgba(255,255,255,0.08);">
                            <svg class="image-placeholder-icon" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="#63AE2C" stroke-width="1.5">
                                <line x1="5" y1="12" x2="19" y2="12"/><line x1="12" y1="5" x2="12" y2="19"/>
                            </svg>
                            <span class="image-placeholder-text" style="color: var(--light);">Calibración láser en taller</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Proceso Creativo -->
    <section id="proceso" class="section-padding container reveal-on-scroll">
        <div class="section-header center">
            <span class="section-subtitle">Atención en Quito</span>
            <h2>Cómo hacemos tu pedido</h2>
            <p>Un flujo de pedido simple, transparente y de trato directo.</p>
        </div>
        
        <div class="process-grid">
            <div class="process-step">
                <div class="process-number">01</div>
                <h4>Nos envías tu logo o idea</h4>
                <p>Elegimos el producto ideal para tu marca o evento.</p>
            </div>
            <div class="process-step">
                <div class="process-number">02</div>
                <h4>Elegimos el producto</h4>
                <p>Elegimos el producto y acabado ideal para el soporte.</p>
            </div>
            <div class="process-step">
                <div class="process-number">03</div>
                <h4>Preparamos una vista previa</h4>
                <p>Revisamos la colocación y proporciones en pantalla.</p>
            </div>
            <div class="process-step">
                <div class="process-number">04</div>
                <h4>Grabamos y entregamos</h4>
                <p>Personalizamos y despachamos tus piezas terminadas.</p>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section class="section-padding container reveal-on-scroll" style="text-align: center; max-width: 800px;">
        <span class="section-subtitle">Hecho en Ecuador</span>
        <h2 style="margin-bottom: 1.25rem;">Cuidamos que cada pieza se vea tan bien como la marca que representa.</h2>
        <p style="margin-bottom: 2rem; font-size: 1rem; color: var(--text-muted);">El acabado final también habla de tu empresa. Por eso cuidamos la selección del producto, la ubicación del logo y la presentación de cada pieza.</p>
        <div class="hero-actions" style="justify-content: center;">
            <a href="https://wa.me/593900000000" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Cotizar por WhatsApp</a>
            <a href="{{ route('cotizacion') }}" class="btn btn-secondary">Enviar mi logo</a>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="{{ asset('js/slider.js') }}"></script>
@endsection
