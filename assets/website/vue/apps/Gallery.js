import Vue from "vue";
// import vuetify from '../plugins/vuetify';
// import store from '../store';

import VueGallery from "../views/gallery/Gallery.vue";
const target = '#vue-gallery'

if(document.querySelector(target)) {
  new Vue({
    // vuetify,
    // store,
    components: { VueGallery },
    template: "<VueGallery/>"
  }).$mount(target);
}

