jQuery("#appoint-form").yiiActiveForm(
  [
    {
      id: "appoint_date",
      name: "appoint_date",
      container: ".field-appoint_date",
      input: "#appoint_date",
      error: ".help-block.invalid-feedback",
      validate: function(attribute, value, messages, deferred, $form) {
        yii.validation.required(value, messages, {
          message: "Appoint Date cannot be blank."
        });
      }
    }
  ],
  []
);
