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
use Illuminate\Support\Facades\URL;  // Import the URL facade (optional if using directly in the helper)
use Illuminate\Support\Facades\Crypt; // Import the Crypt facade
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
    public function trendingProductsWithOffers($limit)
{
    // Fetch the trending products
    $products = DB::table('m27_buy_product')
        ->select(
            'm_buy_prod_name',
            DB::raw('COUNT(m_buy_prod_id) AS buy_count'),
            'p02_product_variant.pr_vari_pr_id',
            'p02_product_variant.pr_vari_id',
            'm16_products.m16_product_status',
            'm16_products.m16_status'
        )
        ->leftJoin('p02_product_variant', 'm27_buy_product.m_buy_prod_id', '=', 'p02_product_variant.pr_vari_id')
        ->leftJoin('m16_products', 'm16_products.m16_id', '=', 'p02_product_variant.pr_vari_pr_id')
        ->where('m27_is_restaurant', 'NO')
        ->whereNotNull('p02_product_variant.pr_vari_id')
        ->where('p02_product_variant.pr_vari_sku', '>', 0)
        ->where('m16_products.m16_product_status', 'approve')
        ->where('m16_products.m16_status', '1')
        ->where('p02_product_variant.pr_vari_status', 1)
        ->groupBy(
            'm_buy_prod_name',
            'p02_product_variant.pr_vari_pr_id',
            'p02_product_variant.pr_vari_id',
            'm16_products.m16_product_status',
            'm16_products.m16_status'
        )
        ->orderByDesc(DB::raw('COUNT(m_buy_prod_id)'))
        ->limit($limit)
        ->get();

    // Preload variant details in a single query to avoid looping queries
    $variantDetails = DB::table('p02_product_variant')
        ->select(
            'p02_product_variant.*',
            'm16_products.m16_id',
            'm16_products.m16_name',
            'm11_unit.m11_name',
            'c01_category.c01_id',
            'c01_category.c01_name',
            'c03_sub_category.c03_id',
            'c03_sub_category.c03_name',
            'c02_pro_category.c02_id',
            'c02_pro_category.c02_name',
            'm16_products.m16_product_status'
        )
        ->join('m16_products', 'm16_products.m16_id', '=', 'p02_product_variant.pr_vari_pr_id')
        ->join('m11_unit', 'm11_unit.m11_id', '=', 'p02_product_variant.pr_vari_unit_id1')
        ->join('c01_category', 'c01_category.c01_id', '=', 'm16_products.m16_main_cat_id')
        ->leftJoin('c03_sub_category', 'c03_sub_category.c03_id', '=', 'm16_products.m16_sub_cat_id')
        ->leftJoin('c02_pro_category', 'c02_pro_category.c02_id', '=', 'c03_sub_category.c03_c02_id')
        ->where('p02_product_variant.pr_vari_status', 1) // Ensure variant is active
        ->limit($limit)
        ->get();

   
    $variantMap = $variantDetails->keyBy('pr_vari_id');

    
    $usedIds = [];  
    $data = [];  

    foreach ($variantMap as $kk => $vv) {
        if ($vv->m16_product_status == 'approve') {
           
            if (!in_array($vv->m16_id, $usedIds)) {
              
                $usedIds[] = $vv->m16_id;

               
                $data[]= [
                    'm16_id' => Crypt::encrypt($vv->m16_id),  // Encrypt m16_id for security
                    'm16_id2' => $vv->m16_id,  // Original m16_id
                    'm16_name' => $vv->m16_name,
                    'c03_id' => $vv->c03_id,
                    'c03_name' => $vv->c03_name,
                    'c02_id' => $vv->c02_id,
                    'c02_name' => $vv->c02_name,
                    'c01_id' => $vv->c01_id,
                    'c01_name' => $vv->c01_name,
                    'pr_vari_id' => Crypt::encrypt($vv->pr_vari_id),  // Encrypt pr_vari_id for security
                    'pr_vari_id2' => $vv->pr_vari_id,  // Original pr_vari_id
                    'pr_vari_amt' => $vv->pr_vari_amt,
                    'pr_vari_dis' => $vv->pr_vari_dis,
                    'pr_vari_umo' => $vv->pr_vari_umo,
                    'pr_vari_sku' => $vv->pr_vari_sku,
                    'pr_vari_default_image' => $vv->pr_vari_default_image,
                   
                    'image_prefix' => url("upload/product/"),  // Generate base URL for product images
                    'pr_seller_martman' => $vv->pr_seller_martman,
                    'pr_vari_status' => $vv->pr_vari_status,
                    'm16_product_status' => $vv->m16_product_status,
                    'm11_name' => $vv->m11_name,
                    'login' => !empty($user_id) ? "TRUE" : "FALSE",  
                    'cartQty' => !empty($user_id) ? $this->_variant_cart_qty($vv->pr_vari_id, $user_id)['qty'] : '0',
                    'wishlist' => !empty($user_id) ? $this->_variant_cart_qty($vv->pr_vari_id, $user_id)['wishlist'] : 'no',
                ];
            }
        }
    }
   //echo "<pre>";
   //$res=(array_shift($data));
//print_r($data);
   return response()->json($data);  
 
 
   
}  
}
