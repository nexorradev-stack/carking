@foreach ($cars as $car_data)
    <div class="my-3">
        <a href="{{ route('advert_detail', ['slug' => $car_data['slug']]) }}" class="text-decoration-none text-dark">
            <div class="main_car_card">
                <div>
                    <div class="car_card_main_img">
                        <div class="car_card_inner_img">
                            <div  class="car_card_background_img" style="background-image: url('{{ asset('' . e($car_data['image'])) }}');"></div>
                            <img src="{{ asset('' . e($car_data['image'])) }}" alt="Car Image"
                                 onload="this.naturalWidth > this.naturalHeight ? this.style.objectFit = 'cover' : this.style.objectFit = 'contain'"
                                onerror="this.src='{{ asset('assets/coming_soon.png') }}'" 
                                 class="car_card_front_img">
                        </div>
                    </div>
                </div>
                <div class="p-3">
                    <p class="car_tittle text-truncate">{{ e($car_data['make'] ?? 'Unknown make') }} {{ e($car_data['model'] ?? 'N/A') }} {{ e($car_data['year'] ?? 'N/A') }}</p>
                    <p class="car_varient text-truncate">
                        @if(empty($car_data['Trim']) || $car_data['Trim'] == 'N/A')
                               {{ strtoupper($car_data['variant']) }}
                        @else
                             {{ strtoupper(e($car_data['Trim'])) }}
                        @endif
                    </p>
                    <div class="car_detail">
                        <div class="text-center">
                            <div class="car_detail_type">
                          
                                <p class="car_detail_type_text">{{ e(isset($car_data['miles']) ? number_format($car_data['miles'], 0, '.', ',') : 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="car_detail_type">
                        
                                <p class="car_detail_type_text">{{ e($car_data['fuel_type'] ?? 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="car_detail_type">
                            
                                <p class="car_detail_type_text">{{ e($car_data['gear_box'] ?? 'N/A') }}</p>
                            </div>
                        </div>
                    </div>
                
                    <div class="height"></div>
                    <div class="car_detail_bottom">
                        <p class="car_price">
                            {{ e(isset($car_data['price']) && $car_data['price'] > 0 ? '£' . number_format($car_data['price'], 0, '.', ',') : 'POA') }}
                        </p>
                        <p class="car_location">
                            {{ $car_data['user']['location'] }}
                        </p>
                    </div>
                </div>
            </div>
        </a>
    </div>
@endforeach