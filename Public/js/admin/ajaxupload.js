//上传图片
var swfu;
function uploadimage(upload_url, placeholder_id, progress_id,post_data,file_upload_limit, file_post_name) {
    if (!file_post_name)
        file_post_name = "Filedata";
    if (!placeholder_id)
        placeholder_id = "spanButtonPlaceHolder";
    if (!progress_id)
        progress_id = "fsUploadProgress";
    file_upload_limit = parseInt(file_upload_limit);
    if(isNaN(file_upload_limit))file_upload_limit = 1;
    if(file_upload_limit < 1)file_upload_limit = 1;
    var button_action;
    if(file_upload_limit == 1){
        button_action = SWFUpload.BUTTON_ACTION.SELECT_FILE;
    }else{
        button_action = SWFUpload.BUTTON_ACTION.SELECT_FILES;
    }
    if(typeof post_data != "object")post_data = {};
    post_data.PHPSESSID = session_id;
    
    var settings_object = {//定义参数配置对象
        flash_url: public + "/swfupload/swfupload/swfupload.swf",
        upload_url: upload_url,
        post_params: post_data,
        file_size_limit: "100 MB",
        file_types: "*.*",
        file_types_description: "All Files",
        file_upload_limit: file_upload_limit, //配置上传个数
        file_queue_limit: 0,
        custom_settings: {
            progressTarget: progress_id,
            cancelButtonId: "btnCancel"
        },
        use_query_string:false,
        debug: false,
        button_action:button_action,  //单选
        // Button settings
        button_image_url: public + "/swfupload/images/TestImageNoText_65x29.png",
        button_width: "65",
        button_height: "29",
        button_placeholder_id: placeholder_id,
        button_text: '<span class="theFont">浏览</span>',
        button_text_style: ".theFont { font-size: 16; }",
        button_text_left_padding: 12,
        button_text_top_padding: 3,
        file_queued_handler: fileQueued,
        file_queue_error_handler: fileQueueError,
        file_dialog_complete_handler: fileDialogComplete,
        upload_start_handler: uploadStart,
        upload_progress_handler: uploadProgress,
        upload_error_handler: uploadError,
        upload_success_handler: uploadSuccess,
        upload_complete_handler: uploadComplete,
        queue_complete_handler: queueComplete,
        debug_handler:debugHandler
    };
    swfu = new SWFUpload(settings_object);//实例化一个SWFUpload，传入参数配置对象
}


