document.addEventListener('DOMContentLoaded', function() {
    const colorPicker = document.getElementById('color_picker');
    const colorCode = document.getElementById('color_code');

    if (colorPicker && colorCode) {
        // Cập nhật màu từ input color sang input text
        colorPicker.addEventListener('input', function() {
            colorCode.value = this.value;
        });

        // Cập nhật màu từ input text sang input color
        colorCode.addEventListener('input', function() {
            if (this.value.match(/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/)) {
                colorPicker.value = this.value;
            }
        });

        // Khởi tạo giá trị ban đầu
        if (!colorCode.value && colorPicker.value) {
            colorCode.value = colorPicker.value;
        } else if (!colorPicker.value && colorCode.value) {
            colorPicker.value = colorCode.value;
        }
    }
});
