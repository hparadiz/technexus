var media = {
    upload: (file) => {
        $.ajax({
            url: '/media/json/upload',
            type: 'PUT',
            data: file,
            contentType: false,
            processData: false,
            cache: false,
            error: function (data,status,xhr) {
                //console.log(a,b,c);
            },
            success: function (data,status,xhr) {
                tinyMCE.activeEditor.execCommand('mceInsertContent', false, '<img src="/media/'+data.data.ID+'" />');
            }
        })
    }
}