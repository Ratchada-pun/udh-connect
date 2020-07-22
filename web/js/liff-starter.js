"use strict";

/**
 * Initialize LIFF
 * @param {string} myLiffId The LIFF ID of the selected element
 */

var udhApp = {
  initializeLiff: function(myLiffId) {
    liff
      .init({
        liffId: myLiffId,
      })
      .then(() => {
        // start to use LIFF's api
        this.initializeApp();
      })
      .catch((err) => {
        console.log(err);
      });
  },
  initializeApp: function() {
    console.log(liff.isLoggedIn());
    if (!liff.isLoggedIn()) {
      // set `redirectUri` to redirect the user to a URL other than the front page of your LIFF app.
        liff.login({ redirectUri: "https://www.udhconnect.info"});  //บน host
      // liff.login({ redirectUri: "https://3be6ec12e718.ngrok.io" }); // run บนเครื่อง
    } else if (liff.isLoggedIn()) {
      //window.location.reload();
      //const accessToken = liff.getAccessToken();ืยท
      //const idToken = liff.getDecodedIDToken();
      //console.log(accessToken); // print decoded idToken object
      liff
        .getProfile()
        .then((profile) => {
          window.localStorage.setItem(this.LIFF_PROFILE, JSON.stringify(profile));
          $("#user-picture").attr("src", profile.pictureUrl);
          this.fetchProflie();
        })
        .catch((err) => {
          console.log("error", err);
        });
    }
  },
  fetchProflie: function() {
    if (liff.isLoggedIn()) {
      var self = this;
      var profile = this.getProfileStorage();
      var path = ["/", "/app/register/policy", "/app/register/create-new-user"];
      $("body").waitMe({
        effect: "roundBounce",
        color: "#ff518a",
      });
      $.ajax({
        method: "GET",
        url: "/app/appoint/profile",
        data: { userId: profile.userId },
        dataType: "JSON",
        success: function(data) {
          if (data) {
            window.localStorage.setItem(self.UDH_PROFILE, JSON.stringify(data));
            if (path.includes(window.location.pathname)) {
              window.location.href = "/app/appoint/create-department";
            }
          }
          $("body").waitMe("hide");
        },
        error: function(jqXHR, textStatus, errorThrown) {
          $("body").waitMe("hide");
          console.log("error", errorThrown);
        },
      });
    }
  },
  getProfileStorage: function() {
    if (window.localStorage.getItem(this.LIFF_PROFILE)) {
      return JSON.parse(window.localStorage.getItem(this.LIFF_PROFILE));
    }
    return null;
  },
  isRegister: function() {
    return window.localStorage.getItem(this.UDH_PROFILE) !== null;
  },
  logout: function() {
    liff.logout();
    window.localStorage.removeItem(this.LIFF_PROFILE);
    window.localStorage.removeItem(this.UDH_PROFILE);
    setTimeout(function() {
      window.location.href = "/";
    }, 1000);
  },
  isLoggedIn: function() {
    return liff.isLoggedIn();
  },
  sendMessages: function(messages) {
    liff
      .sendMessages(messages)
      .then(() => {
        console.log("message sent");
      })
      .catch((err) => {
        Swal.fire({
          title: "เกิดข้อผิดพลาดในการส่งข้อความไลน์",
          text: err,
          icon: "error",
          confirmButtonText: "ตกลง",
          allowOutsideClick: false,
        });
      });
  },
  closeWindow: function() {
    liff.closeWindow();
  },
  LinkRichMenu: function() {//เปลี่ยนเมนู
    // var profile = this.getProfileStorage();
    // var userId = profile.userId;
    var userId = 'Udeadbeefdeadbeefdeadbeefdeadbeef';
    //var richMenuId = "richmenu-349a649ee1b2e2f659ae2da8e24df4ef";
    var richMenuId = "richmenu-349a649ee1b2e2f659ae2da8e24df4ef";
    $.ajax({
      method: "POST",
      url: `https://api.line.me/v2/bot/user/${userId}/richmenu/${richMenuId}`,
      dataType: "JSON",
      beforeSend: function(xhr) {
       //ไลน์พี่บอล
       // xhr.setRequestHeader("Authorization", "Bearer FWZ3P4fRrEXOmhyQtiQFp+TXeSSrkQwGdt3zvp1TezV9gYOruopsbo4YDBjoIKSoWzd/Yx/Ow/8xT0Elwvv6N+akUpPXtdMOdi5NN+t8BMHiVFWoDopJLEn0fUJSg0Rink0gBjXMSwcKIoI6FmoaQQdB04t89/1O/w1cDnyilFU=" );
       xhr.setRequestHeader("Authorization", "Bearer uLF9THsOlQfvth3Y7bvLym0ZwPoEliKF7MszmJq4aymKwWJfYpknJ/zmWwOZsNzgrDXU0+Y7KGMrxCPi79NX1/g3iSeY5Mva1olEL4cwoJtDdznKV+7MjYP89tW6BO8/A//QjXTcoB6BdDt6ooFzB1GUYhWQfeY8sLGRXgo3xvw=" );
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

  LIFF_PROFILE: "liff-profile",
  UDH_PROFILE: "udh-profile",
};

$(window).on("load", function(e) {
 //udhApp.initializeLiff("1621638840-51pLveK0"); //PoonDevelopers
  udhApp.initializeLiff("1654023325-EkWmY9PA");  //UDH-Connect
  // udhApp.initializeLiff("1654009422-Avg5LbQg");
});
