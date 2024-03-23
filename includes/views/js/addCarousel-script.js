jQuery(document).ready(function ($) {
  // กำหนดสถานะเมนู 'List Carousel' เป็นแอคทีฟ
  function setActiveMenu() {
    $("#toplevel_page_how-to-use")
      .removeClass("wp-not-current-submenu")
      .addClass("wp-has-current-submenu");
    $("#toplevel_page_how-to-use > a").addClass("wp-has-current-submenu");
    $('#toplevel_page_how-to-use li a[href$="page=list-carousel"]')
      .parent()
      .addClass("current");
  }

  setActiveMenu();

  // เปิดคลังสื่อ WordPress
  var frame;
  $("#select-image-library").click(function (e) {
    e.preventDefault();

    // สร้าง instance ของ media frame หากยังไม่มี
    if (!frame) {
      frame = wp.media({
        title: adminScriptData.imageSelectTitle,
        button: {
          text: adminScriptData.imageUseButton,
        },
        multiple: false, // อนุญาตให้เลือกรูปภาพได้เพียงอันเดียว
      });

      // จัดการเมื่อมีการเลือกรูปภาพ
      frame.on("select", function () {
        var attachment = frame.state().get("selection").first().toJSON();
        $("#image-library-url").val(attachment.url); // บันทึก URL ของรูปภาพที่เลือกไว้ในฟอร์ม
        $("#image-library-preview").html(
          '<img src="' +
            attachment.url +
            '" style="max-width: 200px; max-height: 200px;">'
        ); // แสดงพรีวิว
      });
    }

    // เปิดคลังสื่อ
    frame.open();
  });
});
