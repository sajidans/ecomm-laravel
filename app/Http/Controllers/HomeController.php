<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\PromotionalBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function homePageCategoryBanner()
    {
        $res = [];
        
        // Fetch active and loadable categories
        $categories = Category::where('c01_status', 1)
        ->where('c01_load', 1)
        ->groupBy('c01_id', 'c01_name')
        ->get(['c01_id', 'c01_name']);
        $categoriesObject = json_decode(json_encode($categories));
        
        foreach ( $categoriesObject as $key => $category) {
           
            // Fetch promotional banners for the category
            $homeBanners = PromotionalBanner::where('co4_category', $category->c01_id)
                ->where('c04_section_type', 9)
                ->limit(20)
                ->get(['co4_banner', 'co4_page_link']);
                $homeBannerssObject = json_decode(json_encode($homeBanners));
                echo "<pre>";
                print_r($category->c01_id);
          
            // foreach ($homeBannerssObject as $k => $banner) {
            //     $res[$key]['category_id'] = base64_encode($category->c01_id);
            //     $res[$key]['category'] = $category->c01_name;
            //     $res[$key]['category_slug'] = ($category->c01_name); // Ensure slug function is defined

            //     $res[$key]['banner'][$k]['page_link'] = $banner->co4_page_link;
            //     $res[$key]['banner'][$k]['banner'] = asset('application/banner/' . $banner->co4_banner);
            // }
        }

        return response()->json($res); // Return the result as JSON response
    }
    public function storeCategories(): JsonResponse
    {
        $categories = Category::where('c01_status', 1)
            ->orderBy('c01_position')
            ->get(['c01_id', 'c01_name', 'c01_image', 'c01_banner_image']);

        $res = $categories->map(function ($category) {
            return [
                'id' => base64_encode($category->c01_id),
                'category' => $category->c01_name,
                'category_slug' => $this->slug($category->c01_name), // Ensure you have this function defined
                'image' => ('https://ssecarts.com/application/IMGCATEGORY/' . $category->c01_image),
                'banner_image' => $category->c01_banner_image,
            ];
        });
      

        return response()->json(['error' => false, 'Data' => $res]);
    }

    // Helper function for slug generation
    protected function slug($string)
    {
        // Slug generation logic here
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }
}
