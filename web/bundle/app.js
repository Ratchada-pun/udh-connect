"use strict";

const Swal = require("sweetalert2");
const _ = require("lodash");
const axios = require("axios");

const LIFF_PROFILE = "liff-profile";
const UDH_PROFILE = "udh-profile";
/**
 * Initialize LIFF
 * @param {string} myLiffId The LIFF ID of the selected element
 */
var config = {
  //ไลน์พี่บอล
  redirectUri: "https://www.udhconnect.info",
  liffId: "1654023325-EkWmY9PA", // line login
  ChannelAccessToken:
    "FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU=",
  RichMenuId: "richmenu-7351dbcc12fcf979942367a8422087b3",
  //RichMenuId: "richmenu-349a649ee1b2e2f659ae2da8e24df4ef",//menu เดิม

  // redirectUri: "https://e98c9d411f42.ap.ngrok.io",
  // liffId: "1654023325-EkWmY9PA", // line login
  // ChannelAccessToken:
  //   "FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU=",
  // RichMenuId: "richmenu-349a649ee1b2e2f659ae2da8e24df4ef",
};

var udhApp = {
  initializeLiff: async function(myLiffId) {
    try {
      await liff.init({
        liffId: myLiffId,
      });
      this.initializeApp();
    } catch (error) {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: _.get(error, "message", "InitializeLiff Error"),
      });
    }
  },
  initializeApp: function() {
    if (!liff.isLoggedIn()) {
      // set `redirectUri` to redirect the user to a URL other than the front page of your LIFF app.
      liff.login({ redirectUri: config.redirectUri }); //บน Longin บน host
    } else {
      this.setProflie();
    }
  },
  setProflie: async function() {
    try {
      this.startLoading();
      var profile = await liff.getProfile();
      window.localStorage.setItem(LIFF_PROFILE, JSON.stringify(profile));
      $("#user-picture").attr("src", profile.pictureUrl);

      var paths = ["/", "/app/register/policy", "/app/register/create-new-user"];
      var response = await axios.get("/app/appoint/profile?userId=" + profile.userId);
      var isMatchRoute =
        RegExp("/", "g").test(window.location.pathname) ||
        RegExp("/app/register/*", "g").test(window.location.pathname);
      if (response.data) {
        window.localStorage.setItem(UDH_PROFILE, JSON.stringify(response.data));

        // if (paths.includes(window.location.pathname)) {
        //   window.location.href = "/app/appoint/create-department";
        // }
      } else if (!window.localStorage.getItem(UDH_PROFILE) && !isMatchRoute) {
        window.location.href = "/";
      } else if (!response.data && !isMatchRoute) {
        window.location.href = "/";
      }

      this.stopLoading();
    } catch (error) {
      this.stopLoading();
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: _.get(error, "message", error),
      });
    }
  },
  getProfileStorage: function() {
    if (window.localStorage.getItem(LIFF_PROFILE)) {
      return JSON.parse(window.localStorage.getItem(LIFF_PROFILE));
    }
    return null;
  },
  isRegister: function() {
    return window.localStorage.getItem(UDH_PROFILE) !== null;
  },
  logout: function() {
    liff.logout();
    window.localStorage.removeItem(LIFF_PROFILE);
    window.localStorage.removeItem(UDH_PROFILE);
    setTimeout(function() {
      window.location.href = "/";
    }, 1000);
  },
  isLoggedIn: function() {
    return liff.isLoggedIn();
  },
  sendMessages: async function(messages) {
    try {
      await liff.sendMessages(messages);
    } catch (error) {
      Swal.fire({
        title: "เกิดข้อผิดพลาดในการส่งข้อความไลน์",
        text: error,
        icon: "error",
        confirmButtonText: "ตกลง",
        allowOutsideClick: false,
      });
    }
  },
  closeWindow: function() {
    liff.closeWindow();
  },
  LinkRichMenu: function() {
    //เปลี่ยนเมนู
    var profile = this.getProfileStorage();
    var userId = profile.userId;
    $.ajax({
      method: "POST",
      url: `https://api.line.me/v2/bot/user/${userId}/richmenu/${config.RichMenuId}`,
      dataType: "JSON",
      beforeSend: function(xhr) {
        //ไลน์พี่บอล
        xhr.setRequestHeader("Authorization", "Bearer " + config.ChannelAccessToken);
      },
      error: function(jqXHR, textStatus, errorThrown) {
        Swal.fire({
          title: "เกิดข้อผิดพลาดในการผูกริชเมนู",
          text: errorThrown,
          icon: "error",
          confirmButtonText: "ตกลง",
          allowOutsideClick: false,
        });
      },
    });
  },

  // loading
  startLoading: function(elm = "body") {
    $(elm).waitMe({
      effect: "roundBounce",
      color: "#ff518a",
    });
  },
  stopLoading: function(elm = "body") {
    $(elm).waitMe("hide");
  },

  getFormData: function(form) {
    //serialize data function
    var formArray = form.serializeArray();

    var returnArray = {};
    for (var i = 0; i < formArray.length; i++) {
      returnArray[formArray[i]["name"]] = formArray[i]["value"];
    }
    return returnArray;
  },
  // ลงทะเบียนผู้ป่วยใหม่
  initFormNewUser: function() {
    var self = this;
    var formId = "#form-signup";
    var form = $(formId);

    $("#reset-form").on("click", function() {
      form.trigger("reset");
    });

    form.on("beforeSubmit", function() {
      self.startLoading(formId);
      var data = self.getFormData(form);
      var profile = self.getProfileStorage();
      $.ajax({
        url: form.attr("action"),
        type: form.attr("method"),
        data: Object.assign(data, profile),
        dataType: "JSON",
        success: function(data) {
          if (data.success) {
            self.setProflie();
            if (liff.isInClient()) {
              self.sendMessages(data.FlexMessage);
            }

            Swal.fire({
              title: data.message,
              text: "",
              icon: "success",
              showCancelButton: false,
              confirmButtonText: "ตกลง",
              cancelButtonText: "ยกเลิก",
            }).then((result) => {
              if (result.value) {
                self.closeWindow();
              }
            });
          } else {
            Object.keys(data.validate).map((key) => {
              $(form).yiiActiveForm("updateAttribute", key, data.validate[key]);
            });
          }
          self.stopLoading(formId);
        },
        error: function(error) {
          self.stopLoading(formId);
          Swal.fire({
            title: "",
            text: error.responseJSON ? error.responseJSON.message : "Error",
            icon: "error",
            confirmButtonText: "ตกลง",
          });
        },
      });
      return false; // prevent default submit
    });
  },

  // ลงทะเบียนผู้ป่วยเก่า
  initFormOldUser: function() {
    var self = this;
    var formId = "#form-search";
    var form = $(formId);

    $(".form-content, #btn-submit").hide();
    $("#btn-search").on("click", function() {
      self.searchPatient();
    });

    form.on("beforeSubmit", function() {
      self.startLoading(formId);
      var data = self.getFormData(form); // form.serialize();
      var profile = self.getProfileStorage();
      $.ajax({
        url: "/app/register/create-new-user?user=old",
        type: form.attr("method"),
        data: Object.assign(data, profile),
        dataType: "JSON",
        success: function(data) {
          if (data.success) {
            self.clearFormOlduser();
            //udhApp.LinkRichMenu();
            if (liff.isInClient()) {
              self.sendMessages(data.FlexMessage);
            }

            Swal.fire({
              title: data.message,
              text: "",
              icon: "success",
              showCancelButton: false,
              confirmButtonText: "ตกลง",
              cancelButtonText: "ยกเลิก",
            }).then((result) => {
              if (result.value) {
                self.closeWindow();
              }
            });
          } else {
            Object.keys(data.validate).map((key) => {
              $(form).yiiActiveForm("updateAttribute", key, data.validate[key]);
            });
          }
          self.stopLoading(formId);
        },
        error: function(error) {
          self.stopLoading(formId);
          Swal.fire({
            title: "",
            text: error.responseJSON ? error.responseJSON.message : "Error",
            icon: "error",
            confirmButtonText: "ตกลง",
          });
        },
      });

      return false; // prevent default submit
    });
  },
  clearFormOlduser: function() {
    $("#form-search").trigger("reset");
    $(".form-content,#btn-submit").hide();
    $("#first_name").val("");
    $("#last_name").val("");
    $("#cid").val("");
    $("#tel").val("");
    $("#year").val("");
    $("#mounh").val("");
    $("#day").val("");
    $("#btn-search,#filter").show();
  },
  searchPatient: function() {
    var self = this;
    var formId = "#form-search";
    var form = $(formId);

    if (!$("#input-filter").val()) return false;
    var data = form.serialize();
    self.startLoading(formId);

    $.ajax({
      url: "/app/register/search-patient",
      type: form.attr("method"),
      data: data,
      dataType: "JSON",
      success: function(data) {
        self.stopLoading(formId);
        if (!data) {
          $(".form-content").hide();
          Swal.fire({
            title: "Oops!",
            text: "ไม่พบข้อมูล",
            icon: "warning",
            confirmButtonText: "ตกลง",
          });
        } else {
          $("#hn").val(data.hn || "");
          $("#first_name").val(data.firstName || "");
          $("#last_name").val(data.lastName || "");
          if (data.CardID) {
            $("#cid").val(String(data.CardID).replace(/[^0-9]/g, ""));
          }
          if (data.phone) {
            $("#tel").val(String(data.phone).replace(/[^0-9]/g, ""));
          }
          if (data.bday) {
            var year = moment(data.bday).format("YYYY");
            var month = moment(data.bday).format("MM");
            var day = moment(data.bday).format("DD");

            $("#year")
              .val(year)
              .change();
            $("#month")
              .val(month)
              .change();
            $("#day")
              .val(day)
              .change();
          }
          $(".form-content,#btn-submit").show();
          $("#btn-search,#filter").hide();
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        $(".form-content").hide();
        self.stopLoading(formId);
        Swal.fire({
          title: "Error!",
          text: errorThrown,
          icon: "error",
          confirmButtonText: "ตกลง",
        });
      },
    });
  },
};

window.udhApp = udhApp;

$(window).on("load", function(e) {
  udhApp.initializeLiff(config.liffId); //UDH-Connect
});
