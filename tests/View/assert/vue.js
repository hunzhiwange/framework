load('vue');

var vm = new Vue({
  template: html('v8_vue'),
  data: {
    msg: $.msg
  }
})

// exposed by vue-server-renderer/basic.js
renderVueComponentToString(vm, (err, res) => {
  print(res)
})