<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\PromotionalBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Models\BuyProduct;
use App\Models\TranddingProduct;
use App\Models\TranddingProductVariant;
use Illuminate\Support\Facades\DB;
class HomeController extends Controller
{
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
       $cleanedString = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string))); 
    }
    public function homeSectionBanner($bannerType, $limit = 6)
    {
        $res= PromotionalBanner::select('co4_banner', 'co4_page_link', 'c04_section_type')
            ->where('c04_section_type', $bannerType)
            ->limit($limit)
            ->get();
            return response()->json(['error' => false, 'Data' => $res]);
    }
    public function trendingProducts($limit)
    {
        $res= DB::table('m27_buy_product')
        ->select('m_buy_prod_name', DB::raw('COUNT(m_buy_prod_id) AS buy_count'), 'p02_product_variant.pr_vari_pr_id', 'p02_product_variant.pr_vari_id', 'm16_products.m16_product_status', 'm16_products.m16_status')
        ->leftJoin('p02_product_variant', 'm27_buy_product.m_buy_prod_id', '=', 'p02_product_variant.pr_vari_id')
        ->leftJoin('m16_products', 'm16_products.m16_id', '=', 'p02_product_variant.pr_vari_pr_id')
        ->where('m27_is_restaurant', 'NO')
        ->whereNotNull('p02_product_variant.pr_vari_id')
        ->where('p02_product_variant.pr_vari_sku', '>', 0)
        ->where('m16_products.m16_product_status', 'approve')
        ->where('m16_products.m16_status', '1')
        ->where('p02_product_variant.pr_vari_status', 1)
        ->groupBy('m_buy_prod_id')
        ->orderBy('buy_count', 'DESC')
        ->limit($limit)
        ->get();
            return response()->json(['error' => false, 'Data' => $res]);
    }
}
