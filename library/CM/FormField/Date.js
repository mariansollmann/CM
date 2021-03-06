/**
 * @class CM_FormField_Date
 * @extends CM_FormField_Abstract
 */
var CM_FormField_Date = CM_FormField_Abstract.extend({
  _class: 'CM_FormField_Date',

  ready: function() {
    this.bindJquery(this.$('select'), 'change', function() {
      this.trigger('change');
    });
  },

  isEmpty: function(value) {
    return _.isEmpty(value.day) || _.isEmpty(value.month) || _.isEmpty(value.year);
  }
});
