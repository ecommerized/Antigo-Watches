$(".lang_link").click(function(e){
    e.preventDefault();
    $(".lang_link").removeClass('active');
    $(".lang_form").addClass('d-none');
    $(this).addClass('active');

    let form_id = this.id;
    let lang = form_id.split("-")[0];
    console.log(lang);
    $("#"+lang+"-form").removeClass('d-none');
    if(lang == '{{$default_lang}}')
    {
        $(".from_part_2").removeClass('d-none');
    }
    else
    {
        $(".from_part_2").addClass('d-none');
    }
});
