"use strict";

/**
 * Initialize LIFF
 * @param {string} myLiffId The LIFF ID of the selected element
 */
function initializeLiff(myLiffId) {
  liff
    .init({
      liffId: myLiffId
    })
    .then(() => {
      // start to use LIFF's api
      initializeApp();
    })
    .catch(err => {
      console.log(err);
    });
}
function initializeApp() {
  if (!liff.isLoggedIn()) {
    // set `redirectUri` to redirect the user to a URL other than the front page of your LIFF app.
    liff.login();
  } else if (liff.isLoggedIn()) {
    //liff.logout();
    //window.location.reload();
    const accessToken = liff.getAccessToken();
    const idToken = liff.getDecodedIDToken();
    console.log(accessToken); // print decoded idToken object
    liff
      .getProfile()
      .then(profile => {
        console.log(profile);
      })
      .catch(err => {
        console.log("error", err);
      });
  }
  // if (liff.isLoggedIn()) {

  // }
}
$(document).ready(function() {
  initializeLiff("1621638840-51pLveK0");
});
