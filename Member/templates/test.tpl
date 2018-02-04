{pb_params limit=10}

<div class="row">
<div class="col-md">
  Test Some things with Members plugin

  <ul>
  {foreach from=$items item="item"}
    <li>{$item->first_name} {$item->email}</li>
  {/foreach}
  </ul>
</div>


<script>
  var members = {$query->json(1,'',true)};
</script>
{literal}
  <div class="col-md">
    <div id="app">
      {{ message }}
      <ul>
        <li v-for="member in members">
          {{member.id}} {{member.first_name}} {{member.email}}
        </li>
      </ul>

      <select2 v-model="member.state">
        <option>MN</option>
        <option>CO</option>
      </select2>

      <select2 v-model="member" name="member" init="3" callback="/res/member/select2.php" placeholder="Type a name"></select2>

      <input v-model="member.first_name" />
      {{member.first_name}} {{member.last_name}} {{member.gender}}
    </div>
  </div>
  <script>

    // How to pass in additional name:value pairs on the return { part of the data property.
    // Example we want to pass pm.id with the ajax request


    // https://vuejs.org/v2/examples/select2.html
    // https://stackoverflow.com/questions/47588215/select2-with-ajax-remote-data-options-component-in-vuejs

    Vue.component('select2', {
      props: ['name', 'value', 'init', 'required', 'callback', 'placeholder'],
      template: '<select :name="name" v-bind:class="{required: required}" class="vue-select2"></select>',
      watch : {
        value : function(value) {
          $(this.$el).empty().append('<option value="' + value.id + '">' + value.text +'</option>').trigger('change.select2');
        }
      },
      mounted:  function() {
        console.log(this.$refs);
        // Use the callback URL to get initial value
        // callback?id=3 should return one item

        var vm = this;

        var config = {
          width: '100%',
          allowClear: true,
          placeholder: this.placeholder,
        };

        if (this.callback) {
          config.ajax = {
            url: this.callback,
            dataType: 'json'
          }
        }

        $(this.$el).select2(config);

        $(this.$el).on('change', function() {
          if (vm.callback) {
            // Not sure why [0] is needed here, normally use $(el).select2('data').item
            var item = $(this).select2('data')[0];
            vm.$emit('input', item);
            $(this).trigger('select2Data', [item])
          } else {
            vm.$emit('input', this.value);
          }
        });
      }
    });

    /*
    Vue.component('select2v', {
      props: ['options', 'value'],
      template: '#select2-template',
      mounted: function () {
        var vm = this
        $(this.$el)
        // init select2
          .select2({ data: this.options })
          .val(this.value)
          .trigger('change')
          // emit event on change.
          .on('change', function () {
            vm.$emit('input', this.value)
          })
      },
      watch: {
        value: function (value) {
          // update value
          $(this.$el).val(value)
        },
        options: function (options) {
          // update options
          $(this.$el).empty().select2({ data: options })
        }
      },
      destroyed: function () {
        $(this.$el).off().select2('destroy')
      }
    })
    */

    var app = new Vue({
      el: '#app',
      data: {
        message: 'Hello Vue!',
        members: members,
        member: new Object()
      }
    })
  </script>
{/literal}


</div>
