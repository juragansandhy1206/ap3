<?php
    Yii::app()->clientScript->registerCssFile(Yii::app()->theme->baseUrl . '/css/jquery-ui-ac.min.css');
    Yii::app()->clientScript->registerScriptFile(Yii::app()->theme->baseUrl . '/js/jquery-ui.min-ac.js', CClientScript::POS_HEAD);
?>
<div id="pilih-barang" class="medium-6 large-5 columns">
    <div class="panel">
        <div class="row">
            <h5>Pilih Barang: <a href="#" id="tambah-barang-baru" class="button tiny bigfont right" accesskey="g">Tambah baran<span class="ak">g</span></a></h5>
        </div>
        <?php
            if ($tipeCari <= 1):
        ?>
            <div class="row collapse" id="scan-cari-barang">
                <div class="small-2 medium-1 columns">
                    <span class="prefix" id="scan-icon"><i class="fa fa-barcode fa-2x"></i></span>
                </div>
                <div class="small-6 medium-9 columns">
                    <input id="scan" type="text"  placeholder="Scan [B]arcode / Input nama" accesskey="b" autofocus="autofocus"/>
                </div>
                <input type="hidden"  id="scan-hide" name="barang-id" />
                <div class="small-2 medium-1 columns">
                    <a href="#" class="button postfix" id="tombol-tambah-barang"><i class="fa fa-level-down fa-2x fa-rotate-90"></i></a>
                </div>
                <?php
                    switch ($tipeCari):
                    case 0:
                    ?>
                        <div class="small-2 medium-1 columns">
                            <a href="#" class="success button postfix" id="tombol-cari-barang" accesskey="c"><i class="fa fa-search fa-2x"></i></a>
                        </div>
                        <?php
                            break;

                            case 1:
                            ?>
                        <div class="small-2 medium-1 columns">
                            <a href="#" class="success button postfix" id="tombol-cari-tabel" accesskey="c"><i class="fa fa-search-plus fa-2x"></i></a>
                        </div>
                        <?php
                            break;
                                endswitch;
                            ?>
            </div>
            <?php
                else:
                ?>
            <div id="dropdown-barang">
                <?php echo CHtml::label('<span class="ak">1</span> Barcode', 'barcode'); ?>
                <div class="row collapse">
                    <div class="medium-10 columns">
                        <?php echo CHtml::dropDownList('barcode', '', $barangBarcode, ['accesskey' => '1', 'id' => 'barcode-pilih']); ?>
                    </div>
                    <div class="medium-2 columns">
                        <a href="#" id="pilih-barcode" class="button postfix tombol-pilih" accesskey="2"><span class="ak">2</span> Pilih</a>
                    </div>
                </div>
                <?php echo CHtml::label('<span class="ak">3</span> Nama', 'nama'); ?>
                <div class="row collapse">
                    <div class="medium-10 columns">
                        <?php echo CHtml::dropDownList('nama', '', $barangNama, ['accesskey' => '3', 'id' => 'nama-pilih']); ?>
                    </div>
                    <div class="medium-2 columns">
                        <a href="#" id="pilih-nama" class="button postfix tombol-pilih" accesskey="4" ><span class="ak">4</span> Pilih</a>
                    </div>
                </div>
            </div>
        <?php
            endif;
            ?>
    </div>
</div>
<script>
    $("#barcode-pilih").keyup(function (e) {
        if (e.keyCode === 13) {
            $("#pilih-barcode").click();
        }
    });

    $("#nama-pilih").keyup(function (e) {
        if (e.keyCode === 13) {
            $("#pilih-nama").click();
        }
    });

    $(".tombol-pilih").click(function () {
        var barangId = $(this).parent('div').parent('div').find('select').val();
        var datakirim = {
            'barangId': barangId
        };
        var dataurl = "<?php echo $this->createUrl('getbarang') ?>";

        $.ajax({
            data: datakirim,
            url: dataurl,
            type: "POST",
            dataType: "json",
            success: updateFormDetail
        });
    });

    /**
     * Update nilai-nilai pada form input pembelian barang
     * @param json Informasi barang
     * @returns {mixed} Menampilkan form input pembelian barang dan mengisi field yang diperlukan
     */
    function updateFormDetail(info) {
        $("#barang-info").html(info['nama'] + ' <small>' + info['barcode'] + '</small><br /><small>Harga Beli </small>' + info['labelHargaBeli'] + ' <small>Harga Jual </small>' + info['labelHargaJual']);
        $("#barang-id").val(info['barangId']);
        $("#satuan").text(info['satuan']);
        $("#qty").val('');
        $("#harga-beli").val(info['hargaBeli']);
        $("#input-po-detail").slideDown(500);
        $("#qty").focus();
//        $("#harga-jual-raw").html('&nbsp;');
        $("#scan").val("");
    }

    $(document).on("click", "#hitung-harga", function () {
        hitungHargaBarang();
    });


    $("#tambah-barang-baru").click(function () {
        $("#input-barang-baru").slideDown(500);
        $("#Barang_barcode").focus();
    });

    function bersihkanInputBarangBaru() {
        $("#input-barang-baru h5").html("Tambah Barang Baru")
        $("#Barang_barcode").val('');
        $("#Barang_nama").val('');
        $("#Barang_kategori_id").val('');
        $("#Barang_satuan_id").val('');
        $("#Barang_rak_id").val('');
    }

    function kirimBarang(barcode) {
        var datakirim = {
            'barcode': barcode
        };
        var dataurl = "<?php echo $this->createUrl('getbarang') ?>";

        $.ajax({
            data: datakirim,
            url: dataurl,
            type: "POST",
            dataType: "json",
            success: updateFormDetail
        });
    }

    $(function () {
        $("#scan").autocomplete("disable");
        $(document).on('click', "#tombol-tambah-barang", function () {
            kirimBarang($("#scan").val());
            return false;
        });
        $(document).on('click', "#tombol-cari-barang", function () {
            $("#scan").autocomplete("enable");
            var nilai = $("#scan").val();
            $("#scan").autocomplete("search", nilai);
            $("#scan").focus();
        });
        $(document).on('click', "#tombol-cari-tabel", function () {
            var datakirim = {
                'cariBarang': true,
                'namaBarang': $("#scan").val()
            };
            $('#barang-grid').yiiGridView('update', {
                data: datakirim
            });
            $("#barang-list").show(100, function () {
                $("#tombol-cari-tabel").focus();
            });

            return false;
        });
    });


    $("#scan").autocomplete({
        source: "<?php echo $this->createUrl('caribarang', ['profilId' => $pembelianModel->profil_id]); ?>",
        minLength: 2,
        delay: 1000,
        search: function (event, ui) {
            $("#scan-icon").html('<img src="<?php echo Yii::app()->theme->baseUrl; ?>/css/3.gif" />');
        },
        response: function (event, ui) {
            $("#scan-icon").html('<i class="fa fa-barcode fa-2x"></i>');
        },
        select: function (event, ui) {
            console.log(ui.item ?
                    "Nama: " + ui.item.label + "; Barcode " + ui.item.value :
                    "Nothing selected, input was " + this.value);
            if (ui.item) {
                $("#scan").val(ui.item.value);
                $("#scan-hide").val(ui.item.id);
            }
        }
    }).autocomplete("instance")._renderItem = function (ul, item) {
        return $("<li style='clear:both'>")
                .append("<a><span class='ac-nama'>" + item.label + "</span> <span class='ac-value'><i>" + item.value + "</i></span></a>")
                .appendTo(ul);
    };

    $("#scan").keydown(function (e) {
        if (e.keyCode === 13) {
            $("#tombol-tambah-barang").click();
        }
    });

</script>
<div id="barang-list" class="medium-6 large-7 columns"  style="display:none">
    <?php
        $this->renderPartial('_barang_list', [
                'barang'        => $barangList,
                'curSupplierCr' => $curSupplierCr,
            ]);
        ?>
</div>
<div id="input-barang-baru" class="medium-6 large-7 columns" style="display: none">
    <?php
        $formInputBaru = $this->beginWidget('CActiveForm', [
                'id'                   => 'barang-baru-form',
                'action'               => $this->createUrl('tambahbarangbaru', ['id' => $pembelianModel->id]),
                'enableAjaxValidation' => false,
                // 'htmlOptions' => array("onsubmit" => "return false;")
            ]);
        ?>
    <div class="panel">
        <h5>Tambah Barang Baru</h5>
        <div class="row">
            <div class="medium-5 large-4 columns">
                <?php echo $formInputBaru->labelEx($barang, 'barcode'); ?>
<?php echo $formInputBaru->textField($barang, 'barcode', ['size' => 45, 'maxlength' => 45, 'autocomplete' => 'off']); ?>
<?php echo $formInputBaru->error($barang, 'barcode', ['class' => 'error']); ?>
            </div>
            <div class="medium-7 large-8 columns">
                <?php echo $formInputBaru->labelEx($barang, 'nama'); ?>
<?php echo $formInputBaru->textField($barang, 'nama', ['size' => 45, 'maxlength' => 45, 'autocomplete' => 'off']); ?>
<?php echo $formInputBaru->error($barang, 'nama', ['class' => 'error']); ?>
            </div>
        </div>
        <div class="row">
            <div class="medium-6 large-4 columns">
                <?php
                    echo $formInputBaru->labelEx($barang, 'kategori_id');
                        echo $formInputBaru->dropDownList($barang, 'kategori_id', CHtml::listData(KategoriBarang::model()->findAll(['order' => 'nama']), 'id', 'nama'), [
                            'empty' => 'Pilih satu..',
                        ]);
                        echo $formInputBaru->error($barang, 'kategori_id', ['class' => 'error']);
                    ?>
            </div>
            <div class="medium-6 large-4 columns">
                <?php
                    echo $formInputBaru->labelEx($barang, 'satuan_id');
                        echo $formInputBaru->dropDownList($barang, 'satuan_id', CHtml::listData(SatuanBarang::model()->findAll(['order' => 'nama']), 'id', 'nama'), [
                            'empty' => 'Pilih satu..',
                        ]);
                        echo $formInputBaru->error($barang, 'satuan_id', ['class' => 'error']);
                    ?>
            </div>
            <div class="medium-6 large-4 columns">
                <?php
                    echo $formInputBaru->labelEx($barang, 'rak_id');
                        echo $formInputBaru->dropDownList($barang, 'rak_id', CHtml::listData(RakBarang::model()->findAll(['order' => 'nama']), 'id', 'nama'), [
                            'empty' => 'Pilih satu..',
                        ]);
                        echo $formInputBaru->error($barang, 'rak_id', ['class' => 'error']);
                    ?>
            </div>
        </div>
        <div class="row">
            <div class="span-12 columns">
                <?php
                    echo CHtml::ajaxLink('Simpan (Alt+m)', $this->createUrl('tambahbarangbaru', [
                            'id' => $pembelianModel->id]), [
                            'type'    => 'POST',
                            'success' => "function (data) {
                                    if (data.sukses){
                                       $('#input-barang-baru').slideUp(500);
                                       updateFormDetail(data);
                                       bersihkanInputBarangBaru();
                                    } else {
                                       $('#input-barang-baru h5').html(data.msg);
                                    }
                              }",
                        ], [
                            'id'        => 'tombol-tambah-barang-baru',
                            'class'     => 'tiny bigfont button',
                            'accesskey' => 'm',
                        ]);
                    ?>
                <a class="tiny bigfont button" id="tombol-batal" href="#" accesskey="l" onclick="$('#input-barang-baru').slideUp(500);
                        bersihkanInputBarangBaru();">Bata<span class="ak">l</span>
                </a>
            </div>
        </div>
    </div>
    <?php $this->endWidget();?>
    <script>
        $("#barang-baru-form").submit(function () {
            return false;
        });

        $("#Barang_barcode").keyup(function (e) {
            if (e.keyCode === 13) {
                $("#Barang_nama").focus();
                $("#Barang_nama").select();
            }
        });
    </script>
</div>
<div id="input-po-detail" class="medium-6 large-7 columns" style="display: none">
    <?php
        $form = $this->beginWidget('CActiveForm', [
                'id'                   => 'po-detail-form',
                'action'               => $this->createUrl('tambahbarang', ['id' => $pembelianModel->id]),
                'enableAjaxValidation' => false,
            ]);
        ?>
    <input type="hidden" name="barang-id" id="barang-id" value="" />
    <input type="hidden" name="input-detail" value="1" />
    <div class="panel">
        <h5><span id="barang-info"></span></h5>
        <div class="row collapse">
            <div class="row">
                <div class="medium-5 columns">
                    <?php echo CHtml::label('Harga Beli', 'hargabeli', ['id' => 'label-harga-beli']) ?>
<?php echo CHtml::textField('hargabeli', '', ['id' => 'harga-beli', 'autocomplete' => 'off']); ?>
                </div>
            </div>
            <div class="row">
                <div class="medium-5 columns">
                    <?php echo CHtml::label('<u><b>J</b></u>umlah Order', 'qty') ?>
                    <div class="row collapse">
                        <div class="small-9 columns">
                            <?php echo CHtml::textField('qty', '', ['accesskey' => 'j', 'autocomplete' => 'off']); ?>
                        </div>
                        <div class="small-3 columns">
                            <span class="postfix"><b><span id="satuan"></span></b></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row collapse">
                <div class="span-12 columns">
                    <?php
                        $focusSetelahTambah = $tipeCari > 1 ? "#barcode-pilih" : "#scan";
                            echo CHtml::ajaxSubmitButton('Tambah (Alt+a)', $this->createUrl('tambahbarang', [
                                'id' => $pembelianModel->id]), [
                                'type'    => 'POST',
                                'success' => "function () {
                                        $.fn.yiiGridView.update('po-detail-grid');
                                        updateTotal();
                                        $('{$focusSetelahTambah}').focus();
                                        $('#input-po-detail').slideUp(500);
                                    }",
                            ], [
                                'id'        => 'tombol-tambah',
                                'class'     => 'tiny bigfont button',
                                'accesskey' => 'a',
                            ]);
                        ?>
                    <a class="tiny bigfont button" id="tombol-batal" href="#" accesskey="l" onclick="$('#input-po-detail').slideUp(500);">Bata<span class="ak">l</span></a>
                </div>
            </div>
        </div>
        <?php $this->endWidget();?>
    </div>
<script>
    $("#po-detail-form").submit(function () {
            return false;
        });
</script>