<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\xlAddRequestProduct;
use App\Http\Requests\xlAddRequestDmucLevel;
use Illuminate\Support\Str;
use App\Models\TableProduct;
use App\Models\TableBrand;
use App\Models\TableProductType;
use App\Models\TableColor;
use App\Models\TableSize;
use App\Models\TableAlbum;
use App\Models\TableVariantsColorProduct;
use App\Models\TableVariantsSizeProduct;
use Illuminate\Support\Facades\File;


class ProductController extends Controller
{

    // ---------------- ADMIN ---------------- //
    // Sản phẩm //
    public function index_product(Request $req)
    {
        $limit =  10;
        //latest() = orderBy('created_at','desc')
        $dsProduct = TableProduct::latest()->paginate($limit);
        //kiểm tra xem nhập keyword chưa
        if ($req->keyword != null) {
            $dsProduct = TableProduct::where('name', 'like', '%' . $req->keyword . '%')->latest()->paginate($limit);
        }
        // lấy trang hiện tại
        $current = $dsProduct->currentPage();
        // lấy số thứ tự đầu tiên nhưng theo dạng mảng (là số 0)
        $perSerial = $limit * ($current - 1);
        $serial = $perSerial + 1;
        return view('.admin.product.main.list', compact('dsProduct', 'serial'));
    }

    public function index_addpro()
    {
        $level1 = TableBrand::all();
        $level2 = TableProductType::all();

        $dsColor = TableColor::all();
        $dsSize = TableSize::all();

        return view('.admin.product.main.add', compact('level1', 'level2', 'dsColor', 'dsSize'));
    }

    public function addproducts(xlAddRequestProduct $req)
    {

        $random = Str::random(5);

        // tạo 1 item mới
        $itemproduct = new TableProduct();
        // lưu các mục vào csdl
        $itemproduct->code = $req->masp;
        $itemproduct->name = $req->tensp;
        $itemproduct->content = htmlspecialchars($req->noidung);
        // kiểm tra xem giá có rỗng không, có giá trị thì thay thế ký tự , =  ký tự rỗng, ngược lại thì gán = 0 
        $itemproduct->price_regular = (isset($req->giagoc) && !empty($req->giagoc)) ? str_replace(',', '', $req->giagoc) : 0;
        $itemproduct->sale_price = (isset($req->giamoi) && !empty($req->giamoi)) ? str_replace(",", "", $req->giamoi) : 0;
        ($req->brand > 0) ? $itemproduct->id_brand = $req->brand : 0;
        ($req->type > 0) ? $itemproduct->id_type = $req->type : 0;
        $itemproduct->quantity = $req->soluong;
        if ($req->file != null) {
            // kiểm tra kích thước
            $size = $req->file->getSize();
            if ($size > 51200) {
                return "Dung lượng hình ảnh lớn. Dung lượng cho phép <= 50MB ~ 51200KB";
            }
            // lọc ra đuôi file
            $extension = $req->file->getClientOriginalExtension();
            if ($extension == 'jpg' || $extension == 'png' || $extension = 'jpeg') {
                // đổi tên hình
                $filename = 'product-' . $random . '.' . $req->file->getClientOriginalExtension();
                // lấy tên file để lưu vào csdl
                $itemproduct->photo = $filename;
                //Lưu trữ file vào thư mục product trong public -> upload -> product
                $req->file->move(public_path('upload/product/'), $filename);
            } else {
                return "Định dạng ảnh không đúng. Định dạng cho phép (.jpg|.png|.jpeg)";
            }
        }
        $itemproduct->save();
        if (!empty($req->color)) {
            foreach ($req->color as $key => $value) {
                $variantsColPro = new TableVariantsColorProduct();
                $variantsColPro->id_product = $itemproduct->id;
                $variantsColPro->id_color = $value;
                $variantsColPro->save();
            }
        }
        if (!empty($req->color)) {
            foreach ($req->size as $key => $value) {    
                $variantsSizPro = new TableVariantsSizeProduct();
                $variantsSizPro->id_product = $itemproduct->id;
                $variantsSizPro->id_size = $value;
                $variantsSizPro->save();
            }
        }
        //kiểm tra xem có file ko
        if(!empty($req->filenames))
        {
            foreach($req->filenames as $image)
            {
                // tạo random mới
                $randomNew = Str::random(5);
                // tạo mới hình con
                $picture = new TableAlbum();
                $destinationPath = public_path('upload/album/');
                $filename = 'pictureProduct-' . $randomNew . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);
                $picture->id_product  = $itemproduct->id;
                $picture->photo = $filename;
                $picture->save();
            }
            
        }
        return redirect()->route('san-pham-admin');
    }

    public function index_modifypro(Request $req, $id)
    {
        $product = TableProduct::find($id);
        $level1 = TableBrand::all();
        $level2 = TableProductType::all();

        $dsColor = TableColor::all();
        $dsSize = TableSize::all();

        // Lấy danh sách color theo sản phẩm và size theo sản phẩm
        $listSelectedColor = TableVariantsColorProduct::where('id_product', $id)->get();
        $listSelectedSize = TableVariantsSizeProduct::where('id_product', $id)->get();
        // Lấy mảng id từ danh sách color theo sản phẩm
        $arrIdColor = [];
        foreach ($listSelectedColor as $k => $v) {
            array_push($arrIdColor, $v->id_color);
        }

        // Lấy mảng id từ danh sách size theo sản phẩm
        $arrIdSize = [];
        foreach ($listSelectedSize as $k => $v) {
            array_push($arrIdSize, $v->id_size);
        }

        return view('.admin.product.main.modify', ['detailSP'  => $product], compact('level1', 'level2', 'dsColor', 'dsSize', 'arrIdColor', 'arrIdSize'));
    }

    public function modifyproducts(xlAddRequestProduct $req, $id)
    {
        // tạo 1 chuỗi ngẫu nhiên 
        $random = Str::random(5);

        //tìm xem sản phẩm có hay không
        $itemproduct = TableProduct::find($id);
        if ($itemproduct == null) {
            return "không tìm thấy sản phẩm nào có ID = {$id} này";
        }
        $itemproduct->code = $req->masp;
        $itemproduct->name = $req->tensp;
        $itemproduct->content = htmlspecialchars($req->noidung);
        // kiểm tra xem giá có rỗng không, có giá trị thì thay thế ký tự ',' =  ký tự rỗng '' , ngược lại thì gán = 0 
        $itemproduct->price_regular = (isset($req->giagoc) && !empty($req->giagoc)) ? str_replace(",", "", $req->giagoc) : 0;
        $itemproduct->sale_price = (isset($req->giamoi) && !empty($req->giamoi)) ? str_replace(",", "", $req->giamoi) : 0;
        ($req->brand > 0) ? $itemproduct->id_brand = $req->brand : 0;
        ($req->type > 0) ? $itemproduct->id_type = $req->type : 0;
        $itemproduct->quantity = $req->soluong;
        if ($req->file != null) {
            // kiểm tra kích thước
            $size = $req->file->getSize();
            if ($size > 51200) {

                return "Dung lượng hình ảnh lớn. Dung lượng cho phép <= 50MB ~ 51200KB";
            }
            // lọc ra đuôi file
            $extension = $req->file->getClientOriginalExtension();
            if ($extension == 'jpg' || $extension == 'png' || $extension = 'jpeg') {
                // đổi tên hình
                $filename = 'product-' . $random . '.' . $req->file->getClientOriginalExtension();
                // lấy tên file để lưu vào csdl
                $itemproduct->photo = $filename;
                //Lưu trữ file vào thư mục product trong public -> upload -> product
                $req->file->move(public_path('upload/product/'), $filename);
            } else {

                return "Định dạng ảnh không đúng. Định dạng cho phép (.jpg|.png|.jpeg)";
            }
        }

        $itemproduct->save();

        // Xoá đi để thêm lại cái mới
        TableVariantsColorProduct::where('id_product', $id)->delete();
        if (!empty($req->color)) {
            // Tìm trong bảng có sản phẩm nào không
            $variantsColPro = TableVariantsColorProduct::where('id_product', $id)->get();

            // Update lại
            foreach ($req->color as $key => $value) {
                $variantsColPro = new TableVariantsColorProduct();
                $variantsColPro->id_product = $id;
                $variantsColPro->id_color = $value;
                $variantsColPro->save();
            }
        }
        // Xoá đi để thêm lại cái mới
        TableVariantsSizeProduct::where('id_product', $id)->delete();
        if (!empty($req->size)) {
            $variantsSizPro = TableVariantsSizeProduct::where('id_product', $id)->get();

            // Update lại
            foreach ($req->size as $key => $value) {
                $variantsSizPro = new TableVariantsSizeProduct();
                $variantsSizPro->id_product = $id;
                $variantsSizPro->id_size = $value;
                $variantsSizPro->save();
            }
        }
        // xoá đi để thêm lại cái mới
        $arr_picture = TableAlbum::where('id_product', $id)->get();
        foreach($arr_picture as $k => $v){
            $image_path = public_path('upload/album/'.$v->photo);
            if(file_exists($image_path)) {
                unlink($image_path);
            }
            $v->delete();
        }
        
        //kiểm tra xem có file ko
        if($req->hasFile('filenames'))
        {
            foreach($req->filenames as $image)
            {
                // tạo random mới
                $randomNew = Str::random(5);
                // tạo mới hình con
                $picture = new TableAlbum();
                $destinationPath = public_path('upload/album/');
                $filename = 'pictureProduct-' . $randomNew . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $filename);
                $picture->id_product  = $itemproduct->id;
                $picture->photo = $filename;
                $picture->save();
            }
            
        }
        return redirect()->route('san-pham-admin');
    }

    public function deleteproducts(Request $req)
    {
        $products = TableProduct::find($req->id);
        if ($products == null) {
            return "không tìm thấy sản phẩm nào có ID = {$req->id} này";
        }

        $image_path = public_path('upload/product/'.$products->photo);
        if(file_exists($image_path)) {
            unlink($image_path);
        }

        $products->delete();
        return redirect()->route('san-pham-admin');
    }
    // Sản phẩm //

    // Danh mục thương hiệu //
    public function index_brand(Request $req)
    {
        $limit =  10;
        $dslevel1 = TableBrand::latest()->paginate($limit);
        //kiểm tra xem nhập keyword chưa
        if ($req->keyword != null) {
            $dslevel1 = TableBrand::where('name', 'like', '%' . $req->keyword . '%')->latest()->paginate($limit);
        }
        // lấy trang hiện tại
        $current = $dslevel1->currentPage();
        // lấy số thứ tự đầu tiên nhưng theo dạng mảng (là số 0)
        $perSerial = $limit * ($current - 1);
        $serial = $perSerial + 1;
        return view('.admin.product.brand.list', compact('dslevel1', 'serial'));
    }

    public function index_addbrand()
    {
        return view('.admin.product.brand.add');
    }

    public function addlevel1(xlAddRequestDmucLevel $req)
    {
        // tạo 1 item mới
        $itemlevel1 = new TableBrand();
        // lưu các mục vào csdl
        $itemlevel1->name = $req->tendm;
        $itemlevel1->save();
        return redirect()->route('sanpham-lv1-admin');
    }

    public function index_modifybrand($id)
    {
        $level1 = TableBrand::find($id);
        return view('.admin.product.brand.modify', ['detailLV1'  => $level1]);
    }

    public function modifylevel1(xlAddRequestDmucLevel $req, $id)
    {
        $itemlevel1 = TableBrand::find($id);
        if ($itemlevel1 == null) {
            return "không tìm thấy danh mục nào có ID = {$id} này";
        }
        // lưu các mục vào csdl
        $itemlevel1->name = $req->tendm;
        $itemlevel1->save();
        return redirect()->route('sanpham-lv1-admin');
    }

    public function deletelevel1(Request $req)
    {
        $itemlevel1 = TableBrand::find($req->id);
        if ($itemlevel1 == null) {
            return "không tìm thấy sản phẩm có ID = {$req->id} ";
        }

        $itemlevel1->delete();
        return redirect()->route('sanpham-lv1-admin');
    }

    // Danh mục thương hiệu //

    // Danh mục loại //
    public function index_type(Request $req)
    {
        $limit =  10;
        $dslevel2 = TableProductType::latest()->paginate($limit);
        //kiểm tra xem nhập keyword chưa
        if ($req->keyword != null) {
            $dslevel2 = TableProductType::where('name', 'like', '%' . $req->keyword . '%')->latest()->paginate($limit);
        }
        // lấy trang hiện tại
        $current = $dslevel2->currentPage();
        // lấy số thứ tự đầu tiên nhưng theo dạng mảng (là số 0)
        $perSerial = $limit * ($current - 1);
        $serial = $perSerial + 1;
        return view('.admin.product.type.list', compact('dslevel2', 'serial'));
    }

    public function index_addtype()
    {
        return view('.admin.product.type.add');
    }

    public function addlevel2(xlAddRequestDmucLevel $req)
    {
        // tạo 1 item mới
        $itemlevel2 = new TableProductType();
        // lưu các mục vào csdl
        $itemlevel2->name = $req->tendm;
        $itemlevel2->save();
        return redirect()->route('sanpham-lv2-admin');
    }

    public function index_modifytype($id)
    {
        $level2 = TableProductType::find($id);

        return view('.admin.product.type.modify', ['detailLV2'  => $level2]);
    }

    public function modifylevel2(xlAddRequestDmucLevel $req, $id)
    {
        $itemlevel2 = TableProductType::find($id);
        if ($itemlevel2 == null) {
            return "không tìm thấy danh mục nào có ID = {$id} này";
        }
        // lưu các mục vào csdl
        $itemlevel2->name = $req->tendm;
        $itemlevel2->save();
        return redirect()->route('sanpham-lv2-admin');
    }

    public function deletelevel2(Request $req)
    {
        $itemlevel2 = TableProductType::find($req->id);
        if ($itemlevel2 == null) {
            return "không tìm thấy sản phẩm có nào ID = {$req->id} này";
        }

        $itemlevel2->delete();
        return redirect()->route('sanpham-lv2-admin');
    }

    // Danh mục loại //

    public function setStatus(Request $req)
    {
        if ($req->id) {
            // lấy giá trị cột được chọn tạo ra 1 mảng riêng
            $status_detail = TableProduct::where('id', $req->id)->pluck('status');
            // cắt mảng thành từng phần tử nhỏ và lấy phần tử đầu tiên
            $status_array = (!empty($status_detail[0])) ? explode(',', $status_detail[0]) : array();
            // check xem $req truyền vào có trong mảng ko
            if (array_search($req->status, $status_array) !== false) {
                $key = array_search($req->status, $status_array);
                unset($status_array[$key]);
            } else {
                array_push($status_array, $req->status);
            }
            // tạo 1 mảng mới
            $data = array();
            // truyền dữ liệu vào mảng
            $data['status'] = (!empty($status_array)) ? implode(',', $status_array) : null;
            // lấy dữ liệu item hiện tại
            $status_save = TableProduct::find($req->id);
            // đổi giá trị cột status thành giá trị mới truyền
            $status_save->status = $data['status'];
            $status_save->save();
        }
    }

    

    // ---------------- ADMIN ---------------- //

    // ---------------- USER ---------------- //
    public function GetProductIndex(Request $req)
    {
        $dsProductNew = TableProduct::whereRaw('FIND_IN_SET("moi", status)')->where('quantity', '>', 0)->get();
        $dsProductOutsanding = TableProduct::whereRaw('FIND_IN_SET("noibat", status)')->where('quantity', '>', 0)->get();

        return view('.user.home.home', compact('dsProductNew', 'dsProductOutsanding'));
    }


    public function GetProductPage(Request $req)
    {
        $limit = 12;
        $dsProduct = TableProduct::whereRaw('FIND_IN_SET("hienthi", status)')->where('quantity', '>', 0)->latest()->paginate($limit);
        return view('.user.product.product', compact('dsProduct'));
    }

    /* Format money */
    public function formatMoney($price = 0, $unit = 'vnđ', $html = false)
    {
        $str = '';
        if ($price) {
            $str .= number_format($price, 0, ',', '.');
            if ($unit != '') {
                if ($html) {
                    $str .= '<span>' . $unit . '</span>';
                } else {
                    $str .= $unit;
                }
            }
        }
        return $str;
    }
    // ---------------- USER ---------------- //    
}
