(function ($) {
    "use strict";

    var HT = {};
    const ajaxLocationUrl = window.ajaxLocationUrl;
    var $province_id = "{{ old('province_id', isset($user) ? $user->province_id : '') }}";
    var $district_id = "{{ old('district_id', isset($user) ? $user->district_id : '') }}";
    var $ward_id = "{{ old('ward_id', isset($user) ? $user->ward_id : '') }}";

    HT.getLocation = () => {
        $(document).on("change", ".location", function () {
            let _this = $(this);
            let option = {
                data: {
                    locationId: $(this).val(),
                },
                target: $(this).attr("data-target"),
            };

            HT.sendDataToGetLocation(option);
        });
    };

    HT.sendDataToGetLocation = (option) => {
    $.ajax({
        url: ajaxLocationUrl,
        type: "GET",
        data: option,
        dataType: "json",
        success: function (response) {
            if (response.html && option.target === "district") {
                $(".district").html(response.html);
                if (option.data.selected_id) {
                    $(".district").val(option.data.selected_id);
                }
            }

            if (response.html && option.target === "wards") {
                $(".wards").html(response.html);
                if (option.data.selected_id) {
                    $(".wards").val(option.data.selected_id);
                }
            }
        },
        error: function (error) {
            console.log(error);
        },
    });
};

   HT.loadCity = () => {
    if ($province_id !== "") {
        $('.province').val($province_id);

        HT.sendDataToGetLocation({
            data: {
                locationId: $province_id,
                selected: $district_id, // Gửi district_id để server chọn đúng option
            },
            target: "district",
        });

        setTimeout(() => {
            HT.sendDataToGetLocation({
                data: {
                    locationId: $district_id,
                    selected: $ward_id, // Gửi ward_id để server chọn đúng option
                },
                target: "wards",
            });
        }, 300); // delay một chút để đợi district load xong
    }
};



    $(document).ready(function () {
        HT.getLocation();
        HT.loadCity();
    });
})(jQuery);
