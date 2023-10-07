</div>
</div>
</div>
</div>
</div>
<script src="<?= base_url('assets/bootstrap-select-1.12.1/js/bootstrap-select.min.js') ?>"></script>
<script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
<script src="<?= base_url('assets/js/placeholders.min.js') ?>"></script>
<script>
    var urls = {
        uploadOthersImages: '<?= base_url('vendor/uploadOthersImages') ?>',
        loadOthersImages: '<?= base_url('vendor/loadOthersImages') ?>',
        removeSecondaryImage: '<?= base_url('vendor/removeSecondaryImage') ?>',
        changeVendorOrdersOrderStatus: '<?= base_url('vendor/changeOrderStatus') ?>'
    };
    function show(parentid,childid) {
        var parent = document.getElementById(parentid)
        var child=document.getElementById(childid);
        if (child.style.display === "none") {     
            child.style.display = "";
            if(parentid==="id_menu_realtime")
            {
                parent.innerHTML =  "<span style='font-weight:bold;font-size:15px;'>▼<?= lang('vendor_order_manage') ?></span>"
            }
        }
        else{     
            child.style.display = "none"; 
            if(parentid==="id_menu_realtime")
            {
                parent.innerHTML = "<span style='font-weight:bold;font-size:15px;'>▶<?= lang('vendor_order_manage') ?></span>"
            }		   
        }
    }    
</script>
<script src="<?= base_url('assets/js/vendors.js') ?>"></script>
</body>
</html>