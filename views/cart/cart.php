<?php $this->layout("layouts/default", ["title" => APPNAME]) ?>

<?php $this->start("page") ?>

<main>
    <div class="container my-5">
    <?php
        if (isset($errors)) {
            foreach ($errors as $err) {
                echo "<p class='text-danger fw-bold'>" . $err . "</p>";
            }
        }
        ?>
        <div class="row table-product">
            <table class="table text-center">
                <thead class="bg-info text-light">
                    <tr>
                        <th scope="col">STT</th>
                        <th scope="col">SẢN PHẨM</th>
                        <th scope="col"></th>
                        <th scope="col">SỐ LƯỢNG</th>
                        <th scope="col">THÀNH TIỀN</th>
                        <th scope="col">XÓA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    $sumProduct = 0;
                    foreach ($carts as $index => $item) {

                        echo '<tr class="align-middle">
            <th scope="row">' . $index + 1 . '</th>
            <td class="col-2"> <a href="/detail?masp=' . $item->ma_sach . '""><img src="/img/product/';
                        echo  $item->hinh_anh . '" style="width: 120px" ></a>';
                        $total = $total + $item['gia_khuyen_mai'] * $item['so_luong_sach'];
                        $sumProduct += $item['so_luong_sach'];

                        echo '</td>
            <td><p class="text-dark text-start">' . $item['ten_sach'] . '</p> <p class="text-dark text-start fw-bold">' . number_format($item->gia_khuyen_mai, 0, '.', ',') . 'đ</p></td>
            <td class="d-flex justify-content-center" style="padding-top:4.1rem; padding-bottom:5rem"><form  action="/del" method="POST"><input type="hidden" value="1"  name="so-luong">
            <input type="hidden" name="masp" value="' . $item->ma_sach . '">
             <button class="btn btn-link"><i class="fa fa-minus-circle" style="color:#ec4276; font-size:24px;"></i></button></form><input type="number" style="width:60px;" class="text-center" value = "' . $item['so_luong_sach'] . '"/><form  action="/addCart" method="POST"><input type="hidden" value="1"  name="so-luong">
            <input type="hidden" name="masp" value="' . $item->ma_sach . '">
             <button class="btn btn-link"><i class="fa fa-plus-circle" style="color:#ec4276; font-size:24px;"></i></button></form></td>
            <td>' . number_format($item['gia_khuyen_mai'] * $item['so_luong_sach'], 0, '.', ',')  . ' VNĐ</td>
            <td><a href="/delCart?masp=' . $item['ma_sach'] . '"><button class="btn btn-danger text-light"><i class="fas fa-trash-alt"></i></button></td></a>
        </tr>

        ';
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php $_SESSION['subtotal'] =$sumProduct; ?>
        <a href="/product_all" class="text-secondary fw-bold">⬅️ Tiếp tục mua sắm</a>
        <div class="row total justify-content-between mt-5">
            <div class="col-sm-8 col-md-10 col-lg-5">
                <h5 class="text-center border-bottom border-dark py-3"> ĐƠN HÀNG </h5>
                <div class="row my-3 text-right">
                    <p class="col-8 fw-bold">Tổng số lượng 📜</p>
                    <div class="col-4 text-lg-right"><?= $sumProduct ?></div>
                </div>
                <div class="row my-2">
                    <p class="col-8 fw-bold">Tổng tiền 💰 </p>
                    <div class="col-4 text-lg-right"><?= number_format($total, 0, '.', ',') ?> VNĐ</div>
                </div>
                <div class="row my-2">
                    <p class="col-6 fw-bold">Hình thức thanh toán 📦</p>
                    <div class="col-6 text-lg-right">Thanh toán khi nhận hàng</div>
                </div>
            </div>
            <div class="col-sm-8 col-md-10 col-lg-5">
                <h5 class="text-center border-bottom border-dark py-3">THÔNG TIN NHẬN HÀNG</h5>
                <div class="panel-body p-1">
                    <form class="form-horizontal" role="form" method="POST" action="/pay">
                        <input type="hidden" name="tong-tien" value="<?= $total ?>">
                        <div class="mb-2 form-group">
                            <label for="name" class="col-md-4 control-label">☎️ Số điện thoại<code>*</code></label>
                            <div>
                                <input id="phone" type="text" class="form-control" name="phone" value="<?= $this->e(\App\SessionGuard::user()->phone) ?>" required>
                                <?php if (isset($errors['phone'])) : ?>
                                    <span class="help-block">
                                        <strong><?= $this->e($errors['phone']) ?></strong>
                                    </span>
                                <?php endif ?>
                            </div>
                        </div>

                        <div class="mb-2 form-group<?= isset($errors['address']) ? ' has-error' : '' ?>">
                            <label for="address" class="col-md-4 control-label">🏡 Địa chỉ<code>*</code></label>
                            <div>
                                <input id="address" type="text" class="form-control" name="address" value="<?= $this->e(\App\SessionGuard::user()->address) ?>" required>

                                <?php if (isset($errors['address'])) : ?>
                                    <span class="help-block">
                                        <strong><?= $this->e($errors['address']) ?></strong>
                                    </span>
                                <?php endif ?>
                            </div>
                        </div>

                </div>
            </div>
            <div class="modal fade" id="confirm-pay" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Xác nhận đặt hàng</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Bạn có chắc chắn muốn đặt hàng không?
                        </div>
                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Xác nhận</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
            <div class="row mt-5 mb-3 justify-content-center">
                <button class="btn btn-primary w-25 text-light" data-bs-toggle="modal" data-bs-target="#confirm-pay">ĐẶT HÀNG</button>
            </div>
        </div>
</main>

<?php $this->stop() ?>