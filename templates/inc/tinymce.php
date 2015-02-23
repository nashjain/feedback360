<script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>
<script>
    $(document).ready(function () {
        tinymce.init({
            selector: "textarea",
            plugins: [
                "advlist autolink lists link image charmap hr anchor pagebreak",
                "wordcount visualblocks visualchars code fullscreen",
                "insertdatetime nonbreaking table contextmenu",
                "emoticons paste textcolor"
            ],
            toolbar1: "undo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | forecolor backcolor emoticons",
            convert_urls: true
        });
    });
</script>