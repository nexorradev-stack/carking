<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class CarController extends Controller
{
    public function search_car(Request $request)
{
    $validated = $request->validate([
        'make' => 'nullable|string|max:255',
        'fuel_type' => 'nullable|string|max:255',
        'model' => 'nullable|string|max:255',
        'variant' => 'nullable|string|max:255',
        'year_from' => 'nullable|integer|min:1900|max:2024',
        'price_from' => 'nullable|numeric|min:0',
        'seller_type' => 'nullable|string|max:255',
        'transmission_type' => 'nullable|string|max:255',
        'year_to' => 'nullable|integer|min:1900|max:2024',
        'price_to' => 'nullable|numeric|min:0',
        'miles' => 'nullable|numeric|min:0',
        'body_type' => 'nullable|string|max:255',
        'engine_size' => 'nullable|string|max:255',
        'doors' => 'nullable|integer|min:0',
        'colors' => 'nullable|string',
        'keyword' => 'nullable|string|max:255',
        'sort' => 'nullable|string|in:most-recent,low-high,high-low,mileage,mileage-low,newest,oldest',
    ]);


    $query = Car::whereHas('advert', function ($query) {
        $query->where('status', 'active');
    });

    
    if (!empty($validated['make'])) {
        $query->where('make', 'like', '%' . $validated['make'] . '%');
    }
    if (!empty($validated['model'])) {
        $query->where('model', 'like', '%' . $validated['model'] . '%');
    }
    if (!empty($validated['variant'])) {
        $query->where('variant', 'like', '%' . $validated['variant'] . '%');
    }
    if (!empty($validated['fuel_type'])) {
        $query->where('fuel_type', 'like', '%' . $validated['fuel_type'] . '%');
    }
    if (!empty($validated['seller_type'])) {
        $query->where('seller_type', 'like', '%' . $validated['seller_type'] . '%');
    }
    if (!empty($validated['transmission_type'])) {
        $query->where('transmission_type', 'like', '%' . $validated['transmission_type'] . '%');
    }
    if (!empty($validated['miles'])) {
        $query->where('miles', '<=', $validated['miles']);
    }
    if (!empty($validated['body_type'])) {
        $query->where('body_type', 'like', '%' . $validated['body_type'] . '%');
    }
    if (!empty($validated['engine_size'])) {
        $query->where('engine_size', '=', $validated['engine_size']);
    }
    if (!empty($validated['doors'])) {
        $query->where('doors', '=', $validated['doors']);
    }
    if (!empty($validated['colors'])) {
        $query->where('colors', 'like', '%' . $validated['colors'] . '%');
    }

    if (!empty($validated['year_from'])) {
        $query->where('year', '>=', $validated['year_from']);
    }
    if (!empty($validated['year_to'])) {
        $query->where('year', '<=', $validated['year_to']);
    }
    if (!empty($validated['price_from'])) {
        $query->where('price', '>=', $validated['price_from']);
    }
    if (!empty($validated['price_to'])) {
        $query->where('price', '<=', $validated['price_to']);
    }if (!empty($validated['keyword'])) {
        $keyword = $validated['keyword'];
        $query->where(function ($q) use ($keyword) {
            $q->where('make', 'like', '%' . $keyword . '%')
              ->orWhere('model', 'like', '%' . $keyword . '%')
              ->orWhere('variant', 'like', '%' . $keyword . '%')
              ->orWhere('fuel_type', 'like', '%' . $keyword . '%')
              ->orWhere('body_type', 'like', '%' . $keyword . '%')
              ->orWhere('colors', 'like', '%' . $keyword . '%');
        });
    }
    if (!empty($validated['sort'])) {
        switch ($validated['sort']) {
            case 'low-high':
                $query->orderBy('price', 'asc'); 
                break;
            case 'high-low':
                $query->orderBy('price', 'desc'); 
                break;
            case 'mileage':
                $query->orderBy('miles', 'asc'); 
                break;
            case 'mileage-low':
                $query->orderBy('miles', 'desc'); 
                break;
            case 'newest':
                $query->orderBy('year', 'desc');
                break;
            case 'oldest':
                $query->orderBy('year', 'asc'); 
                break;
            case 'most-recent':
            default:
                $query->orderBy('created_at', 'desc'); 
                break;
        }
    } else {
       
        $query->orderBy('created_at', 'desc');
    }
    $totalCount = (clone $query)->count();
    $perPage = 20;
    $cars = $query->paginate($perPage)->appends($validated);

    if ($request->ajax()) {
        $html = view('partials.car_list', compact('cars'))->render();
        return response()->json([
            'html' => $html,
            'next_page_url' => $cars->nextPageUrl(),
            'current_page' => $cars->currentPage(),
            'last_page' => $cars->lastPage(),
        ]);
    }
  

    $count = $cars->count();
    $makeselected=$request->input('make');
    $modelselected=$request->input('model');
    $variantselected=$request->input('variant');
    $fuel_typeselected=$request->input('fuel_type');
    $milesselected=$request->input('miles');
    $seller_typeselected=$request->input('seller_type');
    $gear_boxselected=$request->input('gear_box');
    $body_typeselected=$request->input('body_type');
    
    $doorsselected=$request->input('doors');
    $engine_sizeselected=$request->input('engine_size');
    $colorsselected=$request->input('colors');
    $pricefromselected=$request->input('price_from');
    $pricetoselected=$request->input('price_to');
    $yeartoselected=$request->input('year');
    $yearfromselected=$request->input('year_from');

    $year_ranges = [2000, 2001, 2002, 2003, 2004, 2005, 2006, 2007, 2008, 2009, 
                2010, 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019, 
                2020, 2021, 2022, 2023, 2024];
                
    $year_counts = [];

    foreach ($year_ranges as $year) {
        $year_counts[$year] = Car::where('year', $year)->count(); // Exact match for year
    }
    $price_ranges = [
        ['min' => 500, 'max' => 1000],
        ['min' => 1000, 'max' => 1500],
        ['min' => 1500, 'max' => 2000],
        ['min' => 2000, 'max' => 2500],
        ['min' => 2500, 'max' => 3000],
        ['min' => 3000, 'max' => 3500],
        ['min' => 3500, 'max' => 4000],
        ['min' => 4000, 'max' => 4500],
        ['min' => 4500, 'max' => 5000],
        ['min' => 5000, 'max' => 5500],
        ['min' => 5500, 'max' => 6000],
        ['min' => 6000, 'max' => 6500],
        ['min' => 6500, 'max' => 7000],
        ['min' => 7000, 'max' => 7500],
        ['min' => 7500, 'max' => 8000],
        ['min' => 8000, 'max' => 8500],
        ['min' => 8500, 'max' => 9000],
        ['min' => 9000, 'max' => 9500],
 
       
        ['min' => 10000, 'max' => 20000],
        ['min' => 20000, 'max' => 30000],
        ['min' => 30000, 'max' => 40000],
        ['min' => 40000, 'max' => 50000],
        ['min' => 50000, 'max' => 60000],
        ['min' => 60000, 'max' => 70000],
        ['min' => 70000, 'max' => 80000],
        ['min' => 80000, 'max' => 90000],
        ['min' => 90000, 'max' => 100000],
        ['min' => 100000, 'max' => 200000],
      
    ];
    $price_counts = [];


    foreach ($price_ranges as $range) {
        $count = Car::whereBetween('price', [$range['min'], $range['max']])->count();
        $price_counts[] = [
            'min' => $range['min'],
            'max' => $range['max'],
            'count' => $count
        ];
    }
    $miles_ranges = [
        ['min' => 0, 'max' => 10000],
        ['min' => 10000, 'max' => 20000],
        ['min' => 20000, 'max' => 30000],
        ['min' => 30000, 'max' => 40000],
        ['min' => 40000, 'max' => 50000],
        ['min' => 50000, 'max' => 60000],
        ['min' => 60000, 'max' => 70000],
        ['min' => 70000, 'max' => 80000],
        ['min' => 80000, 'max' => 90000],
        ['min' => 90000, 'max' => 100000],
    ];

    $miles_counts = [];
    foreach ($miles_ranges as $range) {
        $count = Car::whereBetween('miles', [$range['min'], $range['max']])->count();
        $miles_counts[$range['max']] = $count; // This will create the counts keyed by the maximum value
    }
   
    $search_field = [
        'make' => Car::select('make', DB::raw('COUNT(*) as count'))
        ->where('make', '!=', 'N/A') 
        ->groupBy('make')
        ->orderBy('make')
        ->get(),
        'model' => Car::select('model', DB::raw('COUNT(*) as count'))
        ->where('model', '!=', 'N/A') 
        ->groupBy('model')
        ->orderBy('model')
        ->get(),
    'variant' => Car::select('variant', DB::raw('COUNT(*) as count'))
        ->where('variant', '!=', 'N/A') 
        ->groupBy('variant')
        ->orderBy('variant')
        ->get(),

    'fuel_type' => Car::select('fuel_type', DB::raw('COUNT(*) as count'))
         ->where('fuel_type', '!=', 'N/A') 
        ->groupBy('fuel_type')
        ->orderBy('fuel_type')
        ->get(),

    'year' => Car::select('year', DB::raw('COUNT(*) as count'))
        ->groupBy('year')
        ->orderBy('year')
        ->get(),

    'price' => Car::select('price', DB::raw('COUNT(*) as count'))
        ->groupBy('price')
        ->orderBy('price')
        ->get(),

    'seller_type' => Car::select('seller_type', DB::raw('COUNT(*) as count'))
        ->groupBy('seller_type')
        ->orderBy('seller_type')
        ->get()
        ->map(function($item) {
            $item->seller_type = match($item->seller_type) {
                'private_seller' => 'Private',
                'car_dealer' => 'Dealer',
                default => $item->seller_type,
            };
            return $item;
        }),

    'gear_box' => Car::select('gear_box', DB::raw('COUNT(*) as count'))
         ->where('gear_box', '!=', 'N/A') 
        ->groupBy('gear_box')
        ->orderBy('gear_box')
        ->get(),

    'miles' => Car::select('miles', DB::raw('COUNT(*) as count'))
        ->groupBy('miles')
        ->orderBy('miles')
        ->get(),

    'body_type' => Car::select('body_type', DB::raw('COUNT(*) as count'))
        ->groupBy('body_type')
        ->orderBy('body_type')
        ->get(),

    'engine_size' => Car::select('engine_size', DB::raw('COUNT(*) as count'))
        ->where('engine_size', '!=', 'N/A') 
        ->groupBy('engine_size')
        ->orderBy('engine_size')
        ->get(),

    'doors' => Car::select('doors', DB::raw('COUNT(*) as count'))
        ->groupBy('doors')
        ->orderBy('doors')
        ->get(),

    'colors' => Car::select('colors', DB::raw('COUNT(*) as count'))
        ->groupBy('colors')
        ->orderBy('colors')
        ->get(),      
        ];
        return view('forsale_page', compact(
            'cars', 'count', 'search_field', 'makeselected', 'fuel_typeselected',
            'colorsselected', 'engine_sizeselected', 'doorsselected', 'body_typeselected',
            'gear_boxselected', 'seller_typeselected', 'milesselected', 'modelselected',
            'variantselected', 'year_ranges', 'year_counts', 'price_ranges', 'price_counts',
            'pricefromselected', 'pricetoselected', 'yeartoselected', 'yearfromselected','totalCount'
        ));
   
    
}
 
 





public function getFilteredFields(Request $request)
{
  
    $baseQuery = Car::query()
        ->whereHas('advert', function ($query) {
            $query->where('status', 'active');
        });
    
    $filters = [
        'make', 'model', 'variant', 'fuel_type', 'body_type', 
        'engine_size', 'doors', 'colors', 'seller_type', 'gear_box'
    ];
    
    foreach ($filters as $filter) {
        if ($request->has($filter) && $request->get($filter) !== '') {
            $baseQuery->where($filter, $request->get($filter));
        }
    }
    
   
    if ($request->has('miles') && is_numeric($request->get('miles'))) {
        $baseQuery->where('miles', '<=', $request->get('miles'));
    }
    
    
    if ($request->has('yearFrom')) {
        $baseQuery->where('year', '>=', $request->get('yearFrom'));
    }
    
    if ($request->has('yearTo')) {
        $baseQuery->where('year', '<=', $request->get('yearTo'));
    }
    
    if ($request->has('pricefrom')) {
        $baseQuery->where('price', '>=', $request->get('pricefrom'));
    }
    
    if ($request->has('priceto')) {
        $baseQuery->where('price', '<=', $request->get('priceto'));
    }

    $createFilteredQuery = function ($field) use ($baseQuery) {
        return clone $baseQuery->select($field)
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull($field)
            ->groupBy($field);
    };
    
    $filtered_fields = [
        'make' => $createFilteredQuery('make')
        ->where('make', '!=', 'N/A')
        ->get()
        ->map(fn($item) => ['make' => $item->make, 'count' => $item->count]),
            
        'fuel_type' => $createFilteredQuery('fuel_type')
        ->where('fuel_type', '!=', 'N/A')
        ->get()
        ->map(fn($item) => ['fuel_type' => $item->fuel_type, 'count' => $item->count]),
            
        'body_type' => $createFilteredQuery('body_type') 
        ->where('body_type', '!=', 'N/A')
          ->where('body_type', '!=', 'UNLISTED')
        ->get()
        ->map(fn($item) => ['body_type' => $item->body_type, 'count' => $item->count]),
            
       'engine_size' => $createFilteredQuery('engine_size')
        ->where('engine_size', '!=', 'N/AL')
        ->where('engine_size', '!=', '0.0L')
        ->get()
        ->map(fn($item) => ['engine_size' => $item->engine_size, 'count' => $item->count]),

            
        'doors' => $createFilteredQuery('doors')
        ->where('doors', '!=', 0)
        ->get()
            ->map(fn($item) => ['doors' => $item->doors, 'count' => $item->count]),
            
        'colors' => $createFilteredQuery('colors')
        ->where('colors', '!=', 'N/A')
        ->get()
            ->map(fn($item) => ['colors' => $item->colors, 'count' => $item->count]),
            
        'seller_type' => $createFilteredQuery('seller_type')->get()
    ->map(fn($item) => [
        'seller_type' => $item->seller_type === 'car_dealer' ? 'Dealer' : 'Private',
        'original_seller_type' => $item->seller_type,
        'count' => $item->count
    ]),
            
        'gear_box' => $createFilteredQuery('gear_box')
        ->where('gear_box', '!=', 'N/A')
        ->get()
            ->map(fn($item) => ['gear_box' => $item->gear_box, 'count' => $item->count]),
            
        'miles' => $createFilteredQuery('miles')
            ->orderBy('miles', 'asc')
            ->get()
            ->map(fn($item) => ['miles' => $item->miles, 'count' => $item->count]),
            
        'year' => $createFilteredQuery('year')
            ->orderBy('year', 'desc')
            ->get()
            ->map(fn($item) => [
                'yearfrom' => $item->year,
                'yearto' => $item->year,
                'count' => $item->count
            ]),
            
        'price' => $createFilteredQuery('price')
            ->orderBy('price', 'asc')
            ->get()
            ->map(fn($item) => [
                'pricefrom' => $item->price,
                'priceto' => $item->price,
                'count' => $item->count
            ])
    ];
    
    return response()->json($filtered_fields);
}

public function getFilteredFieldssale(Request $request)
{
  
  
    $baseQuery = Car::query()
        ->whereHas('advert', function ($query) {
            $query->where('status', 'active');
        });

    $filters = [
        'make', 'model', 'variant', 'fuel_type', 'body_type', 
        'engine_size', 'doors', 'colors', 'seller_type', 'gear_box'
    ];
    
    foreach ($filters as $filter) {
        if ($request->has($filter) && $request->get($filter) !== '') {
            $baseQuery->where($filter, $request->get($filter));
        }
    }
    

    if ($request->has('miles') && is_numeric($request->get('miles'))) {
        $baseQuery->where('miles', '<=', $request->get('miles'));
    }
    

    if ($request->has('yearfrom') && $request->get('yearfrom') !== '') {
        $baseQuery->where('year', '>=', $request->get('yearfrom'));
    }
    
    if ($request->has('yearto') && $request->get('yearto') !== '') {
        $baseQuery->where('year', '<=', $request->get('yearto'));
    }
    
    
    if ($request->has('pricefrom') && $request->get('pricefrom') !== '') {
        $baseQuery->where('price', '>=', $request->get('pricefrom'));
    }
    
    if ($request->has('priceto') && $request->get('priceto') !== '') {
        $baseQuery->where('price', '<=', $request->get('priceto'));
    }

    
    $createFilteredQuery = function ($field, $excludeFilters = []) use ($baseQuery, $request, $filters) {
        $query = Car::query()
            ->whereHas('advert', function ($q) {
                $q->where('status', 'active');
            });
        
      
        foreach ($filters as $filter) {
            if (!in_array($filter, $excludeFilters) && $request->has($filter) && $request->get($filter) !== '') {
                $query->where($filter, $request->get($filter));
            }
        }
        
        
        if (!in_array('miles', $excludeFilters) && $request->has('miles') && is_numeric($request->get('miles'))) {
            $query->where('miles', '<=', $request->get('miles'));
        }
        

        if (!in_array('year', $excludeFilters)) {
            if ($request->has('yearfrom') && $request->get('yearfrom') !== '') {
                $query->where('year', '>=', $request->get('yearfrom'));
            }
            if ($request->has('yearto') && $request->get('yearto') !== '') {
                $query->where('year', '<=', $request->get('yearto'));
            }
        }
        
  
        if (!in_array('price', $excludeFilters)) {
            if ($request->has('pricefrom') && $request->get('pricefrom') !== '') {
                $query->where('price', '>=', $request->get('pricefrom'));
            }
            if ($request->has('priceto') && $request->get('priceto') !== '') {
                $query->where('price', '<=', $request->get('priceto'));
            }
        }
        
        return $query->select($field)
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull($field)
            ->groupBy($field);
    };
    
 
    $filtered_fields = [
        'make' => $createFilteredQuery('make', ['make'])
            ->where('make', '!=', 'N/A')
            ->get()
            ->map(fn($item) => ['make' => $item->make, 'count' => $item->count]),
            
        'fuel_type' => $createFilteredQuery('fuel_type', ['fuel_type'])
            ->where('fuel_type', '!=', 'N/A')
            ->get()
            ->map(fn($item) => ['fuel_type' => $item->fuel_type, 'count' => $item->count]),
            
        'body_type' => $createFilteredQuery('body_type', ['body_type'])
            ->where('body_type', '!=', 'N/A')
            ->where('body_type', '!=', 'UNLISTED')
            ->get()
            ->map(fn($item) => ['body_type' => $item->body_type, 'count' => $item->count]),
            
        'engine_size' => $createFilteredQuery('engine_size', ['engine_size'])
            ->where('engine_size', '!=', 'N/A')
            ->where('engine_size', '!=', '0.0L')
            ->get()
            ->map(fn($item) => ['engine_size' => $item->engine_size, 'count' => $item->count]),
            
        'doors' => $createFilteredQuery('doors', ['doors'])
            ->where('doors', '!=', 0)
            ->get()
            ->map(fn($item) => ['doors' => $item->doors, 'count' => $item->count]),
            
        'colors' => $createFilteredQuery('colors', ['colors'])
            ->where('colors', '!=', 'N/A')
            ->get()
            ->map(fn($item) => ['colors' => $item->colors, 'count' => $item->count]),
            
        'seller_type' => $createFilteredQuery('seller_type', ['seller_type'])
            ->get()
            ->map(fn($item) => [
                'seller_type' => $item->seller_type === 'car_dealer' ? 'Dealer' : 'Private',
                'original_seller_type' => $item->seller_type,
                'count' => $item->count
            ]),
            
        'gear_box' => $createFilteredQuery('gear_box', ['gear_box'])
            ->where('gear_box', '!=', 'N/A')
            ->get()
            ->map(fn($item) => ['gear_box' => $item->gear_box, 'count' => $item->count]),
            
        'miles' => $createFilteredQuery('miles', ['miles'])
            ->orderBy('miles', 'asc')
            ->get()
            ->map(fn($item) => ['miles' => $item->miles, 'count' => $item->count]),
            
        'year' => $createFilteredQuery('year', ['year'])
            ->orderBy('year', 'desc')
            ->get()
            ->map(fn($item) => [
                'yearfrom' => $item->year,
                'yearto' => $item->year,
                'count' => $item->count
            ]),
            
        'price' => $createFilteredQuery('price', ['price'])
            ->orderBy('price', 'asc')
            ->get()
            ->map(fn($item) => [
                'pricefrom' => $item->price,
                'priceto' => $item->price,
                'count' => $item->count
            ])
    ];
    
    return response()->json($filtered_fields);
}


public function countCars(Request $request)
{
    $validated = $request->validate([
        'make' => 'nullable|string|max:255',
        'fuel_type' => 'nullable|string|max:255',
        'model' => 'nullable|string|max:255',
        'variant' => 'nullable|string|max:255',
        'year_from' => 'nullable|integer|min:1900|max:2024',
        'price_from' => 'nullable|numeric|min:0',
        'seller_type' => 'nullable|string|max:255',
        'transmission_type' => 'nullable|string|max:255',
        'year_to' => 'nullable|integer|min:1900|max:2024',
        'price_to' => 'nullable|numeric|min:0',
        'miles' => 'nullable|numeric|min:0',
        'body_type' => 'nullable|string|max:255',
        'engine_size' => 'nullable|string|max:255',
        'doors' => 'nullable|integer|min:0',
        'colors' => 'nullable|string',
        'keyword' => 'nullable|string|max:255',
    ]);
    
    $query = Car::whereHas('advert', function ($query) {
        $query->where('status', 'active');
    });

    if (!empty($validated['make'])) {
        $query->where('make', 'like', '%' . $validated['make'] . '%');
    }
  
    if (!empty($validated['model'])) {
        $query->where('model', 'like', '%' . $validated['model'] . '%');
    }
    if (!empty($validated['variant'])) {
        $query->where('variant', 'like', '%' . $validated['variant'] . '%');
    }
    if (!empty($validated['fuel_type'])) {
        $query->where('fuel_type', 'like', '%' . $validated['fuel_type'] . '%');
    }
    if (!empty($validated['seller_type'])) {
        $query->where('seller_type', 'like', '%' . $validated['seller_type'] . '%');
    }
    if (!empty($validated['transmission_type'])) {
        $query->where('transmission_type', 'like', '%' . $validated['transmission_type'] . '%');
    }
    if (!empty($validated['miles'])) {
        $query->where('miles', '<=', $validated['miles']);
    }
    if (!empty($validated['body_type'])) {
        $query->where('body_type', 'like', '%' . $validated['body_type'] . '%');
    }
    if (!empty($validated['engine_size'])) {
        $query->where('engine_size', '=', $validated['engine_size']);
    }
    if (!empty($validated['doors'])) {
        $query->where('doors', '=', $validated['doors']);
    }
    if (!empty($validated['colors'])) {
        $query->where('colors', 'like', '%' . $validated['colors'] . '%');
    }


    if (!empty($validated['year_from'])) {
        $query->where('year', '>=', $validated['year_from']);
    }
    if (!empty($validated['year_to'])) {
        $query->where('year', '<=', $validated['year_to']);
    }
    if (!empty($validated['price_from'])) {
        $query->where('price', '>=', $validated['price_from']);
    }
    if (!empty($validated['price_to'])) {
        $query->where('price', '<=', $validated['price_to']);
    }if (!empty($validated['keyword'])) {
        $keyword = $validated['keyword'];
        $query->where(function ($q) use ($keyword) {
            $q->where('make', 'like', '%' . $keyword . '%')
              ->orWhere('model', 'like', '%' . $keyword . '%')
              ->orWhere('variant', 'like', '%' . $keyword . '%')
       
              ->orWhere('fuel_type', 'like', '%' . $keyword . '%')
              ->orWhere('body_type', 'like', '%' . $keyword . '%')
              ->orWhere('colors', 'like', '%' . $keyword . '%');
        });
    }
    
    $count = $query->count();
    
    return response()->json(['count' => $count]);
}


public function sortCars(Request $request)
    {
        $validated = $request->validate([
            'make' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'variant' => 'nullable|string|max:255',
            'year_from' => 'nullable|integer|min:1900|max:2024',
            'price_from' => 'nullable|numeric|min:0',
            'seller_type' => 'nullable|string|max:255',
            'transmission_type' => 'nullable|string|max:255',
            'year_to' => 'nullable|integer|min:1900|max:2024',
            'price_to' => 'nullable|numeric|min:0',
            'miles' => 'nullable|numeric|min:0',
            'body_type' => 'nullable|string|max:255',
            'engine_size' => 'nullable|string|max:255',
            'doors' => 'nullable|integer|min:0',
            'colors' => 'nullable|string',
            'keyword' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:most-recent,low-high,high-low,mileage,mileage-low,newest,oldest',
        ]);

        $query = Car::whereHas('advert', function ($query) {
            $query->where('status', 'active');
        });

      
        if (!empty($validated['make'])) {
            $query->where('make', 'like', '%' . $validated['make'] . '%');
        }
        if (!empty($validated['model'])) {
            $query->where('model', 'like', '%' . $validated['model'] . '%');
        }
        if (!empty($validated['variant'])) {
            $query->where('variant', 'like', '%' . $validated['variant'] . '%');
        }
        if (!empty($validated['fuel_type'])) {
            $query->where('fuel_type', 'like', '%' . $validated['fuel_type'] . '%');
        }
        if (!empty($validated['seller_type'])) {
            $query->where('seller_type', 'like', '%' . $validated['seller_type'] . '%');
        }
        if (!empty($validated['transmission_type'])) {
            $query->where('transmission_type', 'like', '%' . $validated['transmission_type'] . '%');
        }
        if (!empty($validated['miles'])) {
            $query->where('miles', '<=', $validated['miles']);
        }
        if (!empty($validated['body_type'])) {
            $query->where('body_type', 'like', '%' . $validated['body_type'] . '%');
        }
        if (!empty($validated['engine_size'])) {
            $query->where('engine_size', '=', $validated['engine_size']);
        }
        if (!empty($validated['doors'])) {
            $query->where('doors', '=', $validated['doors']);
        }
        if (!empty($validated['colors'])) {
            $query->where('colors', 'like', '%' . $validated['colors'] . '%');
        }

       
        if (!empty($validated['year_from'])) {
            $query->where('year', '>=', $validated['year_from']);
        }
        if (!empty($validated['year_to'])) {
            $query->where('year', '<=', $validated['year_to']);
        }
        if (!empty($validated['price_from'])) {
            $query->where('price', '>=', $validated['price_from']);
        }
        if (!empty($validated['price_to'])) {
            $query->where('price', '<=', $validated['price_to']);
        }
        if (!empty($validated['keyword'])) {
            $keyword = $validated['keyword'];
            $query->where(function ($q) use ($keyword) {
                $q->where('make', 'like', '%' . $keyword . '%')
                  ->orWhere('model', 'like', '%' . $keyword . '%')
                  ->orWhere('variant', 'like', '%' . $keyword . '%')
                  ->orWhere('fuel_type', 'like', '%' . $keyword . '%')
                  ->orWhere('body_type', 'like', '%' . $keyword . '%')
                  ->orWhere('colors', 'like', '%' . $keyword . '%');
            });
        }

     
        $sort = $validated['sort'] ?? 'most-recent';
        switch ($sort) {
            case 'low-high':
                $query->orderBy('price', 'asc');
                break;
            case 'high-low':
                $query->orderBy('price', 'desc');
                break;
            case 'mileage':
                $query->orderBy('miles', 'asc');
                break;
            case 'mileage-low':
                $query->orderBy('miles', 'desc');
                break;
            case 'newest':
                $query->orderBy('year', 'desc');
                break;
            case 'oldest':
                $query->orderBy('year', 'asc');
                break;
            case 'most-recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

       
        $perPage = 20;
        $cars = $query->paginate($perPage)->appends($validated);

      
        return response()->json([
            'html' => view('partials.car_list', compact('cars'))->render(),
            'total' => $cars->total(),
            'next_page_url' => $cars->nextPageUrl(),
            'current_page' => $cars->currentPage(),
            'last_page' => $cars->lastPage(),
        ]);
    }

}
