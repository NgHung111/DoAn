@extends('admin.index')
@section('body')
    <div class="content-wrapper">
        <section class="content-header text-sm">
            <div class="container-fluid">
                <div class="row">
                    <ol class="breadcrumb float-sm-left pl-3">
                        <li class="breadcrumb-item"><a href="{{ route('trang-chu-admin') }}" title="Bảng điều khiển">Bảng điều
                                khiển</a></li>
                        <li class="breadcrumb-item active">Thêm mới sản phẩm</li>
                    </ol>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <form class="validation-form" method="post" action="{{ route('xl-them-moi-san-pham-admin') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="card-footer text-sm sticky-top">
                        <button type="submit" class="btn btn-sm bg-gradient-primary submit-check"><i
                                class="far fa-save mr-2"></i>Lưu</button>
                        {{-- <button type="submit" class="btn btn-sm bg-gradient-success submit-check" name="save-here" disabled><i class="far fa-save mr-2"></i>Lưu tại trang</button> --}}
                        <button type="reset" class="btn btn-sm bg-gradient-secondary"><i class="fas fa-redo mr-2"></i>Làm
                            lại</button>
                        <a class="btn btn-sm bg-gradient-danger" href="{{ route('san-pham-admin') }}" title="Thoát"><i
                                class="fas fa-sign-out-alt mr-2"></i>Thoát</a>
                    </div>
                    <div class="card card-primary card-outline text-sm">
                        <div class="card-header">
                            <h3 class="card-title">Danh mục Product</h3>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                        class="fas fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="form-group-category row">

                                <div class="form-group col-xl-6 col-sm-4">
                                    <label class="d-block" for="id_list">Danh mục cấp 1:</label>
                                    <select id="select-category" name="" class="form-control select2 ">
                                        <option value="0">Chọn Danh mục</option>
                                        @foreach ($level1 as $k => $value)
                                            <option value="{{ $value->id }}">{{ $value->name }}</option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="form-group col-xl-6 col-sm-4">
                                    <label class="d-block" for="id_cat">Danh mục cấp 2:</label>
                                    <select id="" name="" class="form-control select2 ">
                                        <option value="0">Chọn Danh mục</option>
                                        <option value="1">Danh Mục Cấp 2 a</option>
                                        <option value="2">Danh Mục Cấp 2 b</option>
                                        <option value="3">Danh Mục Cấp 2 c</option>
                                    </select>
                                </div>

                                <div class="form-group col-xl-6 col-sm-4">
                                    <label class="d-block" for="id_item">Danh mục cấp 3:</label>
                                    <select id="" name="" class="form-control select2 ">
                                        <option value="0">Chọn Danh mục</option>
                                        <option value="1">Danh Mục Cấp 3 a</option>
                                        <option value="2">Danh Mục Cấp 3 b</option>
                                        <option value="3">Danh Mục Cấp 3 c</option>
                                    </select>
                                </div>

                                <div class="form-group col-xl-6 col-sm-4">
                                    <label class="d-block" for="id_brand">Danh mục hãng:</label>
                                    <select id="" name="" class="form-control select2 ">
                                        <option value="0">Chọn Danh mục</option>
                                        <option value="1">Nike</option>
                                        <option value="2">Adidas</option>
                                        <option value="3">Converse</option>
                                    </select>

                                </div>

                                <div class="form-group col-xl-6 col-sm-4">
                                    <label class="d-block" for="id_color">Danh mục màu sắc:</label>
                                    <select id="" name="" class="form-control select2 ">
                                        <option value="0">Chọn Danh mục</option>
                                        <option value="1">Đỏ</option>
                                        <option value="2">Đen</option>
                                        <option value="3">Xanh</option>
                                        <option value="4">Trắng</option>
                                    </select>
                                </div>

                                <div class="form-group col-xl-6 col-sm-4">
                                    <label class="d-block" for="id_size">Danh mục kích thước:</label>
                                    <select id="" name="" class="form-control select2 ">
                                        <option value="0">Chọn Danh mục</option>
                                        <option value="1">38</option>
                                        <option value="2">39</option>
                                        <option value="3">40</option>
                                        <option value="4">41</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xl-8">
                            <div class="card card-default color-palette-box card-primary card-outline text-sm">
                                <div class="card-header">
                                    <h3 class="card-title">Thông tin sản phẩm</h3>
                                </div>
                                <div class="card-body card-article">
                                    <div class="form-group title">
                                        <label for="name-product">Tên sản phẩm:</label>
                                        <input type="text" class="form-control for-seo text-sm" name="tensp"
                                            id="fullname" placeholder="Tên sản phẩm" @error('tensp') is-invalid @enderror>
                                        @error('tensp') <div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="form-group titleProduct">
                                        <label for="nameProduct">Nội dung:</label>
                                        <textarea class="form-control for-seo text-sm " name="noidung" id="" rows="5"
                                            placeholder="Nội dung "></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label class="d-block" for="code-product">Mã sản phẩm:</label>
                                            <input type="text" class="form-control text-sm" name="masp"
                                                id="code" placeholder="Mã sản phẩm">
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="d-block" for="regular_price">Giá gốc:</label>
                                            <div class="input-group">
                                                <input type="text"
                                                    class="form-control format-price regular_price text-sm" name="giagoc"
                                                    id="regular_price" placeholder="Giá gốc">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><strong>VNĐ</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="d-block" for="sale_price">Giá mới:</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control format-price sale_price text-sm"
                                                    name="giamoi" id="sale_price" placeholder="Giá mới">
                                                <div class="input-group-append">
                                                    <div class="input-group-text"><strong>VNĐ</strong></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-4">

                            <div class="card card-primary card-outline text-sm">
                                <div class="card-header">
                                    <h3 class="card-title">Hình ảnh sản phẩm</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i
                                                class="fas fa-minus"></i></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Image --}}
                                    <div class="photoUpload-zone">
                                        <div class="photoUpload-detail" id="photoUpload-preview">
                                            <img class="rounded"
                                                onerror="src='{{ asset('assets/admin/images/noimage.png') }}'"
                                                src="" alt="Alt Photo" style="" />
                                        </div>
                                        <label class="photoUpload-file" id="photo-zone" for="file-zone">
                                            <input type="file" name="file" id="file-zone">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p class="photoUpload-drop">Kéo và thả hình vào đây</p>
                                            <p class="photoUpload-or">hoặc</p>
                                            <p class="photoUpload-choose btn btn-sm bg-gradient-success">Chọn hình</p>
                                        </label>
                                        <div class="photoUpload-dimension">Width: 270px - Height: 270px
                                            (.jpg|.gif|.png|.jpeg)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>
@endsection