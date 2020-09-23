"use strict";

$('input[name="doc_option"]').on("change", function(e) {
  e.preventDefault();
  // if ($(this).val() === "0") {
  //   $("#doctor").addClass("hidden");
  //   $("#doctor, #doctor_id").val("");
  //   $('input[name="docname"]').prop("checked", false);
  //   ClearForm();
  // } else {
  //   $("#doctor").removeClass("hidden");
  // }
});

function inputEvent() {
  $('input[name="docname"]').on("change", function(e) {
    e.preventDefault();
    $(".appoint-time").html("");
    if ($(this).is(":checked")) {
      $("#doctor").removeClass("hidden");
      $("#doctor").val($(this).data("docname"));
      $("#doctor_id").val($(this).val());
      $(this).prop("checked", true);
      $("#exampleModal3").modal("hide");
      dateList = [];
      GetSchedules($(this).val());
    } else {
      $("#doctor").val("");
      $("#doctor_id").val("");
      $(this).prop("checked", false);
      $("#exampleModal3").modal("hide");
    }
  });
}
inputEvent();

function GetSchedules(docId) {
  $("#appoint-form").waitMe({

    effect: "roundBounce",
    color: "#ff518a",
  });
  $.ajax({
    method: "GET",
    url: "/app/appoint/schedules",
    data: {
      doc_id: docId,
    },
    dataType: "json",
    success: function(data) {
      if (data.length) {
        var dates = []; // YYYY-MM-DD
        for (let index = 0; index < data.length; index++) {
          dates.push(data[index].schedule_date);
        }
        dateList = dates;
        var startDate = moment(data[0].schedule_date).format("DD/MM/YYYY");
        var endDate = moment(data[data.length - 1].schedule_date).format("DD/MM/YYYY");

        var firstDate = moment(data[0].schedule_date); // YYYY-MM-DD
        var lastDate = moment(data[data.length - 1].schedule_date); // YYYY-MM-DD

        var diffDates = lastDate.diff(firstDate, "days");

        let startDateDiff = data[0].schedule_date; // YYYY-MM-DD
        var datesDisabled = [];
        for (let i = 0; i < diffDates; i++) {
          var tomorrow = new Date(startDateDiff);
          tomorrow.setDate(tomorrow.getDate() + 1); // add date
          if (!dateList.includes(moment(tomorrow).format("YYYY-MM-DD"))) {
            datesDisabled.push(moment(tomorrow).format("DD/MM/YYYY"));
          }
          startDateDiff = moment(tomorrow).format("YYYY-MM-DD");
        }
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setStartDate", startDate);
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setEndDate", endDate);
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setDatesDisabled", datesDisabled);
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("update", '');
        //GetScheduleTimes(data[0].schedule_date);
        $("#appoint-form").waitMe("hide");
      } else {
        var startDate = moment().format("DD/MM/YYYY");
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setStartDate", startDate);
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setEndDate", null);
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setDatesDisabled", []);
        jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("update", '');
        $("#appoint-form").waitMe("hide");
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      $("#appoint-form").waitMe("hide");
      Swal.fire({
        title: "Error!",
        text: errorThrown,
        icon: "error",
        confirmButtonText: "ตกลง",
      });
    },
  });
}

function GetScheduleTimes(date) {
  $("#appoint-form").waitMe({
    effect: "roundBounce",
    color: "#ff518a",
  });
  var formArray = objectifyForm();
  $.ajax({
    method: "POST",
    url: "/app/appoint/schedule-times",
    data: {
      ...formArray,
      appoint_date: date,
    },
    dataType: "json",
    success: function(data) {
      $("#doctor").removeClass("hidden");
      $(".appoint-time").html("");
      if (data.schedule_times.length) {
        for (let index = 0; index < data.schedule_times.length; index++) {
          if(data.schedule_times[index].disabled){
            $(".appoint-time").append(`<label class="control control-solid control-solid-success control--radio">
            ${data.schedule_times[index].text}
            <input type="radio" name="AppointModel[appoint_time]" value="${data.schedule_times[index].value}" disabled />
                <span class="control__indicator"></span>
        </label>`);
          }else{
            $(".appoint-time").append(`<label class="control control-solid control-solid-success control--radio">
            ${data.schedule_times[index].text}
            <input type="radio" name="AppointModel[appoint_time]" value="${data.schedule_times[index].value}"  />
                <span class="control__indicator"></span>
        </label>`);
          }
         
        }
      }else{
        $(".appoint-time").html(`<div style="text-align: center;font-size:16pt;color:#ff0000;">ไม่พบเวลาทำการแพทย์ กรุณาเลือกวันนัดหมายใหม่</div>`);
      }
      $("#doctor-list").html(data.list);
      $("#appoint-form").waitMe("hide");
      inputEvent();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      $("#appoint-form").waitMe("hide");
      Swal.fire({
        title: "Error!",
        text: errorThrown,
        icon: "error",
        confirmButtonText: "ตกลง",
      });
    },
  });
}
var form = $("#appoint-form");
function objectifyForm() {
  //serialize data function
  var formArray = form.serializeArray();
  var returnArray = {};
  for (var i = 0; i < formArray.length; i++) {
    returnArray[formArray[i]["name"]] = formArray[i]["value"];
  }
  return returnArray;
}

function ClearForm() {
  $(".appoint-time").html("");
  //$( ".btn-doc-option" ).removeClass('active')
  $(`input[name="doc_option"]`).prop("checked", false);
  $("#doctor").addClass("hidden");
  $("#doctor, #doctor_id").val("");
  $('input[name="docname"]').prop("checked", false);
  dateList = [];
  var startDate = moment().format("DD/MM/YYYY");
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setStartDate", startDate);
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setEndDate", null);
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setDatesDisabled", []);
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("update", startDate);
  $("#appoint-form").trigger("reset");
}

$("#reset-form").on("click", function() {
  ClearForm();
});

jQuery("#appointmodel-appoint_date-kvdate").on("show", function() {
  $("#overlay").removeClass("hidden");
});
jQuery("#appointmodel-appoint_date-kvdate").on("hide", function() {
  $("#overlay").addClass("hidden");
});

$("#appointmodel-appoint_date-kvdate")
  .kvDatepicker()
  .on("changeDate", function(e) {
    GetScheduleTimes(e.format("yyyy-mm-dd")); //เรียกดูตารางเวลาแพทย์
    $("#appointmodel-appoint_date-kvdate").kvDatepicker("hide");
  });

$("#btn-random").on("click", function(e) {
  e.preventDefault();

  randomDoctor();
});

function randomDoctor() {
  var docIds = [];
  var docNames = [];
  $.each($("#exampleModal3").find('input[name="docname"]'), function(index, value) {
    docIds.push($(this).val());
    docNames.push({
      id: $(this).val(),
      name: $(this).data("docname"),
    });
  });
  var doctorId = docIds[Math.floor(Math.random() * docIds.length)];
  var doctor = docNames.find((d) => d.id === doctorId);
  $("#doctor").val(doctor.name);
  $("#doctor_id").val(doctorId);
  $(`input[value="${doctorId}"]`).prop("checked", true);
  var startDate = moment().format("DD/MM/YYYY");
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setStartDate", startDate);
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setEndDate", null);
  jQuery("#appointmodel-appoint_date-kvdate").kvDatepicker("setDatesDisabled", []);
  //jQuery('#appointmodel-appoint_date-kvdate').kvDatepicker('update', startDate);
  GetSchedules(doctorId);
}

// jQuery("#appoint-form").yiiActiveForm(
//   [
//     {
//       id: "appoint_date",
//       name: "appoint_date",
//       container: ".field-appoint_date",
//       input: "#appoint_date",
//       error: ".help-block.invalid-feedback",
//       validate: function(attribute, value, messages, deferred, $form) {
//         yii.validation.required(value, messages, {
//           message: "Appoint Date cannot be blank."
//         });
//       }
//     }
//   ],
//   []
// );

var $form = $("#appoint-form");
$form.on("beforeSubmit", function() {
  var data = $form.serialize();
  var formArray = objectifyForm();

  if (!formArray["AppointModel[appoint_date]"]) {
    Swal.fire({
      title: "Oops...!",
      text: "กรุณาระบุวันที่นัด",
      icon: "warning",
      confirmButtonText: "ตกลง",
    });
    return false;
  }
  if (!formArray["AppointModel[appoint_time]"]) {
    Swal.fire({
      title: "Oops...!",
      text: "กรุณาระบุเวลานัด",
      icon: "warning",
      confirmButtonText: "ตกลง",
    });
    return false;
  }
  $("#appoint-form").waitMe({
    effect: "roundBounce",
    color: "#ff518a",
  });
  $.ajax({
    url: "/app/appoint/save-appoint",
    type: "POST",
    data: {
      ...formArray,
      appoint_time_from: formArray["AppointModel[appoint_time]"]
        ? formArray["AppointModel[appoint_time]"].substring(0, 5)
        : "",
      appoint_time_to: formArray["AppointModel[appoint_time]"]
        ? formArray["AppointModel[appoint_time]"].substring(6, 11)
        : "",
    },
    success: function(data) {
      // Implement successful
      ClearForm();
      var appoint = data.appoint;
      if (liff.isInClient()) {
        udhApp.sendMessages([
          {
            type: "flex",
            altText: "ข้อมูลการจอง",
            contents: {
              type: "bubble",
              size: "giga",
              hero: {
                type: "image",
               // url: "https://docs.google.com/uc?id=1741EturA17E9hZSNGiWkoQ6ri-T28oQe",
                url: "https://www.udhconnect.info/images/logonew.png", //logoใหม่
                size: "full",
                aspectRatio: "30:13",
                aspectMode: "fit",
                backgroundColor: "#eeeeee",
              },
              body: {
                type: "box",
                layout: "vertical",
                spacing: "md",
                backgroundColor: "#eeeeee",
                contents: [
                  {
                    type: "text",
                    text: "ใบนัดหมาย",
                    wrap: true,
                    weight: "bold",
                    gravity: "center",
                    size: "xxl",
                    align: "center",
                    color: "#62cb31",
                  },
                  {
                    type: "box",
                    layout: "vertical",
                    margin: "lg",
                    spacing: "sm",
                    contents: [
                      {
                        type: "box",
                        layout: "baseline",
                        spacing: "sm",
                        contents: [
                          {
                            type: "text",
                            text: "HN :",
                            color: "#aaaaaa",
                            size: "lg",
                            flex: 1,
                          },
                          {
                            type: "text",
                            text: appoint.hn || "ยังไม่มี hn",
                            wrap: true,
                            size: "lg",
                            color: "#666666",
                            flex: 4,
                          },
                        ],
                      },
                      {
                        type: "box",
                        layout: "baseline",
                        spacing: "sm",
                        contents: [
                          {
                            type: "text",
                            text: "ชื่อ",
                            color: "#aaaaaa",
                            size: "lg",
                            flex: 1,
                          },
                          {
                            type: "text",
                            text: appoint.fullname,
                            wrap: true,
                            size: "lg",
                            color: "#666666",
                            flex: 4,
                          },
                        ],
                      },
                      {
                        type: "box",
                        layout: "baseline",
                        spacing: "sm",
                        contents: [
                          {
                            type: "text",
                            text: "แผนก",
                            color: "#aaaaaa",
                            size: "lg",
                            flex: 1,
                          },
                          {
                            type: "text",
                            text: appoint.department_name,
                            wrap: true,
                            color: "#666666",
                            size: "lg",
                            flex: 4,
                          },
                        ],
                      },
                      {
                        type: "box",
                        layout: "baseline",
                        spacing: "sm",
                        contents: [
                          {
                            type: "text",
                            text: "แพทย์",
                            color: "#aaaaaa",
                            size: "lg",
                            flex: 1,
                          },
                          {
                            type: "text",
                            text: appoint.doctor_name,
                            wrap: true,
                            color: "#666666",
                            size: "lg",
                            flex: 4,
                          },
                        ],
                      },
                      {
                        type: "box",
                        layout: "baseline",
                        spacing: "sm",
                        contents: [
                          {
                            type: "text",
                            text: "วันที่",
                            color: "#aaaaaa",
                            size: "lg",
                            flex: 1,
                          },
                          {
                            type: "text",
                            text: appoint.appoint_date,
                            wrap: true,
                            color: "#666666",
                            size: "lg",
                            flex: 4,
                          },
                        ],
                      },
                      {
                        type: "box",
                        layout: "baseline",
                        spacing: "sm",
                        contents: [
                          {
                            type: "text",
                            text: "เวลา",
                            color: "#aaaaaa",
                            size: "lg",
                            flex: 1,
                          },
                          {
                            type: "text",
                            text: appoint.appoint_time + " น.",
                            wrap: true,
                            size: "lg",
                            color: "#666666",
                            flex: 4,
                          },
                        ],
                      },
                    ],
                  },
                ],
              },
              footer: {
                type: "box",
                layout: "vertical",
                backgroundColor: "#eeeeee",
                contents: [
                  {
                    type: "text",
                    text: appoint.hn
                      ? "กรุณากดบัตรคิว ณ จุดบริการ ตามวันและเวลาที่นัดหมาย!"
                      : "กรุณาติดต่อห้องบัตร ตามวันและเวลาที่นัดหมาย!",
                    margin: "xl",
                    size: "xs",
                    color: "#ff0000",
                    style: "normal",
                    align: "center",
                  },
                ],
              },
            },
          },
        ]);
      }

      Swal.fire({
        title: "นัดแพทย์สำเร็จ",
        text: "",
        icon: "success",
        confirmButtonText: "ตกลง",
        allowOutsideClick: false,
      }).then((result) => {
        if (result.value) {
          window.location.href = `/app/appoint/follow-up?hn=${data.hn || ""}&appoint_date=${data.appoint_date}&doctor=${
            data.doctor
          }&cid=${data.id_card}`;
        }
      });
      $("#appoint-form").waitMe("hide");
    },
    error: function(jqXHR, textStatus, errorThrown) {
      $("#appoint-form").waitMe("hide");
      console.log(jqXHR)
      var message = jqXHR.hasOwnProperty('responseJSON') && jqXHR.responseJSON.hasOwnProperty('message') ? jqXHR.responseJSON.message : errorThrown
      Swal.fire({
        title: "ไม่สามารถบันทึกข้อมูลได้!",
        text: message,
        icon: "error",
        confirmButtonText: "ตกลง",
      });
    },
  });
  return false; // prevent default submit
});

$(window).on("load", function(e) {
  var params = yii.getQueryParams(window.location.search);
  if (params.doc_id) {
    var docInput = $("#" + params.doc_id);
    if (docInput) {
      $(".btn-doc-option").removeClass("active");
      $("#doctor").val(docInput.data("docname"));
      $("#doctor_id").val(docInput.val());
      docInput.prop("checked", true);
      $("#option").prop("checked", false);
      $("#option1").prop("checked", true);
      $("#doctor").removeClass("hidden");
      GetSchedules(docInput.val());
    }
  } else {
    $("#option").prop("checked", true);
  }
});
