(($, Drupal) => {
  module.exports = {
    setup(id) {
      const sideBar = $('#' + id + ' .edit-post-sidebar');
      const editMeta = $('#edit-meta');
      // editMeta.prepend(sideBar);

      console.log('toolbar setup', sideBar);
    }
  }
})(jQuery, Drupal);