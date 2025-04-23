/*global wc_avatax_admin_misc*/
(function() {
  "use strict";

  /**
   * WooCommerce AvaTax Admin scripts
   *
   * @since 2.6.0
   */
  jQuery(function($) {
      var accounting, ref, ref1, ref2, ref3, wc_avatax_admin, woocommerce_admin, woocommerce_admin_meta_boxes, wc_avatax_admin_jsontree;
      wc_avatax_admin = (ref = window.wc_avatax_admin_elr) != null ? ref : {};
      woocommerce_admin = (ref1 = window.woocommerce_admin) != null ? ref1 : {};
      woocommerce_admin_meta_boxes = (ref2 = window.woocommerce_admin_meta_boxes) != null ? ref2 : {};
      accounting = (ref3 = window.accounting) != null ? ref3 : {};
        $(document).ready(function() {
            if($("#table_type").val() == 'flat')
            {
              $(".flat-fieldset").show();
              $(".eav-fieldset").hide();
              $(".vertical-fieldset-na").show();
            }else if($("#table_type").val() == 'eav')
            {
              $(".flat-fieldset").hide();
              $(".eav-fieldset").show();
              $(".vertical-fieldset-na").show();
            }
            else if($("#table_type").val() == 'vertical')
            {
              $(".flat-fieldset").hide();
              $(".eav-fieldset").hide();
              $(".vertical-fieldset").show();
              $(".vertical-fieldset-na").hide();
            }
            $(".field-selector-section").show();
            $("#table_type").change(function() {
              if($("#table_type").val() == 'flat')
                {
                  $(".flat-fieldset").show();
                  $(".eav-fieldset").hide();
                  $(".vertical-fieldset-na").show();
                }else if($("#table_type").val() == 'eav')
                {
                  $(".flat-fieldset").hide();
                  $(".eav-fieldset").show();
                  $(".vertical-fieldset-na").show();
                }
                else if($("#table_type").val() == 'vertical')
                {
                  $(".flat-fieldset").hide();
                  $(".eav-fieldset").hide();
                  $(".vertical-fieldset").show();
                  $(".vertical-fieldset-na").hide();
                }
            });
          $(".filter-fields").hide();
          var data = {
              action: 'wc_avatax_submit_map_perform',
              param: 'document_ready',
              entity: getParameterByName('entity')  // Get the 'entity' value from URL query parameters using getParameterByName helper function
          };
          
          jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
            if(response['data'].length > 0)
              {
                $.each(response['data'], function (i, main_table) {
                  $("#secondary_table").append($("<option> </option>")
                      .attr("value", main_table.main_table).text(main_table.main_table));

                  /* $("#mapped_table").append($("<option> </option>")
                    .attr("value", main_table.main_table)
                    .attr("isarray",main_table.isarray)
                    .text(main_table.main_table)); */ /* Commented as duplicate options comming due to this */
                });
                showSchemaTree(JSON.parse(response['schema']), JSON.parse(response['savedSchema']));
                refresh_mapped_table(response['mapperTables']);
                bind_delete_mapping();
              }

          });

          // code to delete conditional mapper record
          bind_delete_mapping();
          
          // code to delete conditional mapper record
          bind_delete_conditional_mapping();

          $('.divmapper').trigger('click');

          $.each($("#ulSchema").jsontree("getSelectedItems"), function( k, v ){
            var path = v["path"].replace("JSON." , "");
            $('#mapper_table_field').append($('<option></option>').val(path).html(path));
          });
          
          $("#btnSubmitConditional").click(function(e){
            e.preventDefault();
            var filterobj = {};
            $("#saveConditionalInfo").html("");
        
            // Validate required fields
            if (!$("#cond_params").val() || !$("#mapped_table").val() || !$("#mapper_table_field").val()) {
                $("#saveConditionalInfo").html("All fields are required");
                return false;
            }
        
            // Handle filter fields if table is array type
            if ($("#mapped_table").val() && $("#mapped_table").val() != "" && $("#mapped_table").find(":selected").attr("isarray") == "1") {
                var hasEmptyFields = false;
                
                $.each($(".filter-fields-field"), function(i, dataField){
                    var filterValue = $(dataField).closest('tr').find('#mapper_table_filter_data').val();
                    if (!$(dataField).val() || !filterValue) {
                        hasEmptyFields = true;
                        return false; // break the loop
                    }
                    filterobj[$(dataField).val()] = filterValue;
                });
        
                if (hasEmptyFields) {
                    $("#saveConditionalInfo").html("Filter fields and values are required");
                    return false;
                }
            }
        
            var filter_data = {
                cond_param:           $("#cond_params").val(),
                mapped_table:         $("#mapped_table").val(),
                mapper_table_field:   $("#mapper_table_field").val(),
                filter_obj:          filterobj
            }
        
            var data = {
                action: 'wc_avatax_submit_map_perform',
                param: 'save_filter_data',
                filterInfo: filter_data
            };
        
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                $("#saveConditionalInfo").html(response['data']);
                $("#tbl_conditional_mapper tbody").html(JSON.parse(response['schema']));
                if (response['code'] == 200) {
                    // Unselect all options for multi-select
                    $('#mapped_table').find('option').prop('selected', false);
                    $('#mapper_table_field').find('option').prop('selected', false);
                    // Trigger the change event
                    $('#mapped_table').trigger('change');
                    $(".filter-fields").hide();
                }
                bind_delete_conditional_mapping();
            });
        });

          $('.elr_container.tabs nav a').on('click', function() {
            show_content($(this).index());
          });
        
          show_content(0);
          
        });

        function getParameterByName(name, url) {
          if (!url) url = window.location.href; // Use the current URL if no URL is provided
          name = name.replace(/[\[\]]/g, '\\$&'); // Escape the parameter name for regex
          var regex = new RegExp('[?&]' + name + '=([^&]*)');
          var results = regex.exec(url);
          var entityType = $("#entity_type").val();
          console.log("Entity Type:", entityType);
      
          // If results are null or empty, return entityType value (if available) or empty string
          if (results === null || results[1] === '') {
              return entityType || '';
          }
      
          // Otherwise return the decoded URL parameter value
          return decodeURIComponent(results[1].replace(/\+/g, ' '));
      }
      

        $("#mapped_table").change(function(e){
          var $mapper_field = $("#mapper_table_field");
          var $mapper_filter_field = $("#mapper_table_filter_field");
          var $mapper_table_filter_data = $("#mapper_table_filter_data");
          var $selectedTableValue = this.value;
          $mapper_field.empty();
          $mapper_filter_field.empty();
          $mapper_table_filter_data.text = "";

          $mapper_field.append($("<option> </option>")
              .attr("value", "").text("Select data field"));
          if($selectedTableValue && $selectedTableValue != "")
          {
            var data = {
            action: 'wc_avatax_submit_map_perform',
            param: 'InvoiceMapper'
          };
          jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
            var $filterTableRecord  = response["data"].find(ele => ele.main_table == $selectedTableValue);
              if ($filterTableRecord) {
                // Using Set to remove duplicates
                let uniqueValues = [...new Set($filterTableRecord["selected_fields"].split(","))];
                uniqueValues.forEach(c => {
                    $mapper_field.append(
                        $("<option/>")
                        .attr("value", c.trim())
                        .text(c.trim())
                    );
                });
                if ($filterTableRecord["isarray"] == true) {
                  $(".filter-fields").show();
                  $("#mapper_table_filter_field").append($("<option> </option>")
                    .attr("value", "").text("Select data field"));
                  
                  // Using Set to remove duplicates
                  let uniqueValues = [...new Set($filterTableRecord["selected_fields"].split(","))];
                  uniqueValues.forEach(c => {
                    $("#mapper_table_filter_field").append(
                      $("<option/>")
                      .attr("value", c.trim())
                      .text(c.trim())
                    );
                  });
                } else {
                  $("#mapper_table_filter_field").empty()
                  $(".filter-fields").hide();
                }
              }
            });
          }
        });

        // Define a Backbone View for the alert
        var AlertView = Backbone.View.extend({
          tagName: 'div',
          className: 'alert-message',
          
          template: _.template(jQuery('#tmpl-wc-avatax-alert-modal').html()),
          
          events: {
              'click .modal-close': 'removeAlert'
          },
          
          initialize: function(options) {
              this.message = options.message || 'Table not found';
              this.render();
          },
          
          render: function() {
              this.$el.html(this.template({ message: this.message }));
              $('body').append(this.$el);

              document.body.classList.add('open');

              return this;
          },
          
          removeAlert: function() {
              // When closing the modal
              document.body.classList.remove('open');
              this.remove();
          }
        });

        $("#main_table").change(function() {
            $("#saveInfo").html("");
            // Get the selected value
            var selectedValue = $("#main_table").val();

            // Check if the field is empty
            if (!selectedValue || selectedValue.trim() === '') {
              var $el = $("#main_table_ref_field");
              var $eav_key = $("#eav_key_field");
              var $eav_value = $("#eav_value_field");

              $el.empty().append($("<option></option>")
                  .attr("value", "Select source table column")
                  .text("Select source table column"));

              $eav_key.empty().append($("<option></option>")
                  .attr("value", "Select column data key")
                  .text("Select column data key"));

              $eav_value.empty().append($("<option></option>")
                  .attr("value", "Select column data value")
                  .text("Select column data value"));

              return;
            }
            
            var data = {
                action: 'wc_avatax_submit_map_perform',
                tablename: $("#main_table").val(),
                param: 'table_dependency'
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                if (response === 0) {
                    return;
                }
                
                if(response['data'])
                {
                    var $el = $("#main_table_ref_field");
                    var $eav_key = $("#eav_key_field");
                    var $eav_value = $("#eav_value_field");
                    $el.empty(); // remove old options
                    $eav_key.empty();
                    $eav_value.empty()
                    if(response['data'].length > 0)
                    {
                        $.each(response['data'], function (i, main_table_ref_field) {
                        $el.append($("<option> </option>")
                            .attr("value", main_table_ref_field).text(main_table_ref_field));
                        $eav_key.append($("<option> </option>")
                            .attr("value", main_table_ref_field).text(main_table_ref_field));
                        $eav_value.append($("<option> </option>")
                            .attr("value", main_table_ref_field).text(main_table_ref_field));
                        
                        });
                    }else{
                        // Usage of backbone alertView:
                        var alertView = new AlertView({
                          message: "Table not found"
                        });
                    }
                }
            });
          });

          function avatax_Block_UI() {
            $("#wc-avatax-block-UI").addClass("wc-avatax-blockUI");
            $("#wc-avatax-block-UI").addClass("blockOverlay");
            $("body").attr("style", "overflow: hidden;");
          }

          function avatax_UnBlock_UI() {
            $("#wc-avatax-block-UI").removeClass("wc-avatax-blockUI");
            $("#wc-avatax-block-UI").removeClass("wc-avatax-blockOverlay");
            $("body").attr("style", "overflow: auto;");
          }

          $("#secondary_table").change(function() {
            // Check if default option is selected
              if ($("#secondary_table").val() === "") {
                var $el = $("#secondary_table_ref_field");
                $el.empty().append($("<option></option>")
                    .attr("value", "")
                    .text("Select reference table field"));
                return;
            }

            var data = {
                action: 'wc_avatax_submit_map_perform',
                tablename: $("#secondary_table").val(),
                param: 'table_dependency'
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                if (response === 0) {
                    return;
                }
                
                if(response['data'])
                {
                    var $el = $("#secondary_table_ref_field");
                    $el.empty(); // remove old options
                    if(response['data'].length > 0)
                    {
                        $.each(response['data'], function (i, secondary_table_ref_field) {
                        $el.append($("<option> </option>")
                            .attr("value", secondary_table_ref_field).text(secondary_table_ref_field));
                        });
                    }else{
                        // Usage of backbone alertView:
                        var alertView = new AlertView({
                          message: "Table not found"
                        });
                    }
                }
            });
          });

          $("#btnSubmitMapper").click(function(e){
            e.preventDefault();
            
            // Function to show error message with timeout
            function showMessage(message, isError = true) {
              // Clear any existing timeout
              if (window.messageTimeout) {
                  clearTimeout(window.messageTimeout);
              }
            
              // Check if the message indicates success
              const isSuccess = message.toLowerCase().includes('mapping saved successfully');
              $("#mapper_message")
                  .css('color', isSuccess ? 'green' : 'red')
                  .text(message)
                  .show();
            
              // Set timeout only for success messages
              if (!isError) {
                  window.messageTimeout = setTimeout(function() {
                      $("#mapper_message").fadeOut('slow', function() {
                          $(this).hide().text('');
                      });
                  }, 10000);
              }
          }
        
            // Function to hide message
            function hideMessage() {
                if (window.messageTimeout) {
                    clearTimeout(window.messageTimeout);
                }
                $("#mapper_message").hide().text('');
            }
            
            // Check if main_table is empty
            if (!$("#main_table").val() || $("#main_table").val().trim() === '') {
                showMessage('Please select a Source table');
                resetFieldsToDefault();
                return false;
            } else {
                hideMessage();
            }
        
            avatax_Block_UI();
            $("#mapper_message").html("").hide();
            $("#btnSubmitMapper").addClass("spin").attr("disabled", "disabled");
        
            var filter_data = {
                table_type:             $("#table_type").val(),
                main_table:             $("#main_table").val(),
                main_table_ref_field:   $("#main_table_ref_field").val(),
                eav_key_field:          $("#eav_key_field").val(),
                eav_value_field:        $("#eav_value_field").val(),
                secondary_table:        $("#secondary_table").val(),
                secondary_table_ref_field:   $("#secondary_table_ref_field").val(),
                main_table_isarray:     $(".main_table_isarray:checked").val(),
                entity_type:            $("#entity_type").val()
            }
        
            var data = {
                action: 'wc_avatax_submit_map_perform',
                filterInfo: filter_data,
                param: 'save_mapping',
                entity: getParameterByName('entity')
            };
        
            jQuery('body').trigger('processStart');
            
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
              if (response === 0) {
                  showMessage('Failed to process the request. Please try again.', true);
                  resetFieldsToDefault();
                  return;
              }
              const message = response['error_save']
              const isSuccess = message.toLowerCase().includes('mapping saved');
              
              if (isSuccess) {
                showMessage('Mapping saved successfully', false);  // Show success with timeout
                
              } else {
                showMessage(response['error_save'], true);  // Show error without timeout
                
              }
              
              $("#saveInfo").html(response['data']);
              refresh_tbl_mapper(response['records']);
              showSchemaTree(JSON.parse(response['schema']), JSON.parse(response['savedSchema']))
              refresh_mapped_table(response['mapperTables']);
              bind_delete_mapping();
              resetFieldsToDefault();
          })
          .fail(function(jqXHR, textStatus, errorThrown) {
              console.error("AJAX Error:", textStatus, errorThrown);
              showMessage('Error occurred while saving', true);
              resetFieldsToDefault();
          })
          .always(function() {
              $("#btnSubmitMapper").removeClass("spin").removeAttr("disabled");
              avatax_UnBlock_UI();
          });
      
          return false;
      });
      
      // Function to reset all fields to their default state
      function resetFieldsToDefault() {
          // Reset main table
          $("#main_table").val('').trigger('change');
          
          // Reset main table reference field
          $("#main_table_ref_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select source table column"));
          
          // Reset EAV key field
          $("#eav_key_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select column data key"));
          
          // Reset EAV value field
          $("#eav_value_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select column data value"));
          
          // Reset secondary table
          $("#secondary_table").val("");
          
          // Reset secondary table reference field
          $("#secondary_table_ref_field").empty()
              .append($("<option></option>")
              .attr("value", "")
              .attr("style", "color: #757575;")
              .text("Select reference table field"));
          
          // Reset array checkbox
          $(".main_table_isarray").prop('checked', false);
      }
        
      
          function convertFormToJSON(form) {
            return $(form)
              .serializeArray()
              .reduce(function (json, { name, value }) {
                json[name] = value;
                return json;
              }, {});
          };

          
          if(wc_avatax_admin_jsontree){
            showSchemaTree(JSON.parse(wc_avatax_admin_jsontree.schema), JSON.parse(wc_avatax_admin_jsontree.savedSchema));
          }
          
          function showSchemaTree(json_obj, selected_nodes){
            $("#ulSchema").jsontree({
              json: json_obj,
              selected_nodes: selected_nodes,
              expand_default: true,
            });
          }

          $("#ulSchema").jsontree("getSelectedItemPaths");

          const ConfirmationView = Backbone.View.extend({
            tagName: 'div',
            className: 'confirmation-dialog',
            
            template: _.template(jQuery('#tmpl-wc-avatax-confirmation-modal').html()),
        
            events: {
                'click .btn-confirm': 'onConfirm',
                'click .modal-close': 'onCancel'
            },
        
            initialize: function() {
                this.render();
            },
        
            render: function() {
                this.$el.html(this.template());
                $('body').append(this.$el);
                return this;
            },
        
            onConfirm: function() {
              // Get the save button
              const saveButton = $("#btnSchemaSave");
              
              // Enable loading state
              saveButton.addClass("spin").prop("disabled", true);
              
              var nodes = $("#ulSchema").jsontree("getSelectedItemPaths");
              var data = {
                  action: 'wc_avatax_submit_map_perform',
                  columns: nodes,
                  param: 'save_schema',
                  entity: getParameterByName('entity')
              };
          
              jQuery.post(wc_avatax_admin.ajax_url, data)
                  .done(function(response) {
                      if (response === 0 || response.error) {
                          $("#btnSchemaSave").after(
                              '<div id="schema_message" style="margin-top:10px;color:#dc3545;display:flex;align-items:center;">' +
                              '<span style="margin-right:5px">✕</span>' +
                              '<span>Failed to send data fields to Avalara. Please try again.</span>' +
                              '</div>'
                          );
                      } else {
                          $("#btnSchemaSave").after(
                              '<div id="schema_message" style="margin-top:10px;color:#4CAF50;display:flex;align-items:center;">' +
                              '<span style="margin-right:5px">✓</span>' +
                              '<span>The selected data fields from WooCommerce are sent successfully to Avalara for mapping.</span>' +
                              '</div>'
                          );
                      }
                  })
                  .fail(function() {
                      $("#btnSchemaSave").after(
                          '<div id="schema_message" style="margin-top:10px;color:#dc3545;display:flex;align-items:center;">' +
                          '<span style="margin-right:5px">✕</span>' +
                          '<span>Network error occurred. Please check your connection and try again.</span>' +
                          '</div>'
                      );
                  })
                  .always(function() {
                      // Disable loading state
                      saveButton.removeClass("spin").prop("disabled", false);
                      
                      // Remove message after delay
                      setTimeout(function() {
                          $("#schema_message").fadeOut('slow', function() {
                              $(this).remove();
                          });
                      }, 10000);
                  });
          
              this.remove();
          },
        
            onCancel: function() {
                this.remove();
            }
          });

          $("#btnSchemaSave").click(function(e){
            e.preventDefault();
            
            // Remove any existing messages
            $("#schema_message").remove();

            // Show confirmation dialog
            new ConfirmationView();

            return false;
        });

          $("#btnSchemaSelectAll").click(function(){
            $("#ulSchema").jsontree("selectAll");
          })

          $("#btnSchemaUnSelectAll").click(function(){
            $("#ulSchema").jsontree("unSelectAll");
          })

          $("#treeNodeSearch").on("keyup change", function(){
            $("#ulSchema").jsontree("expandAll");
            var key = $(this).val();
            if(key !== ""){
              $("#ulSchema .tree-item").hide();
              
              $("#ulSchema .tree-item[data-value*='"+key+"']").each(function(){ $(this).show();});
            }
            else{
              $("#ulSchema .tree-item").show();
              $("#ulSchema").jsontree("expandAll");
            }
          })
          $(document).on('click', '.rowfy-addrow', function(){
            let rowfyable = $(this).closest('table');
            let currentLast = $('tbody tr:last', rowfyable).prev();
            let lastRow = $('tbody tr:last', rowfyable).prev().clone();

            $('input', lastRow).val('');
            $(lastRow).insertAfter(currentLast);
            $(this).removeClass('rowfy-addrow btn-success').addClass('rowfy-deleterow btn-danger').text('-');
          });

          $(document).on('click', '.rowfy-deleterow', function(){
            $(this).closest('tr').remove();
          });          

          function show_content(index) {
            // Make the content visible
            $('.elr_container.tabs .content.visible').removeClass('visible');
            $('.elr_container.tabs .content:nth-of-type(' + (index + 1) + ')').addClass('visible');
          
            // Set the tab to selected
            $('.elr_container.tabs nav a.selected').removeClass('selected');
            $('.elr_container.tabs nav a:nth-of-type(' + (index + 1) + ')').addClass('selected');
          }

          $('#entity_type').on('change', function() {
            // Get current URL
            var currentUrl = window.location.href;
            
            // Get selected value
            var selectedValue = $(this).val();
            
            // Store the selected value before reload
            localStorage.setItem('avatax_entity_type', selectedValue);
            
            // Create new URL object
            var url = new URL(currentUrl);
            
            // Update the entity parameter
            url.searchParams.set('entity', selectedValue);
            
            // Update URL without page reload
            window.history.pushState({}, '', url.toString());
            
            // Reload the page
            window.location.href = url.toString();
        });
        
        // Add this to handle the value after page reload
        $(document).ready(function() {
            var storedValue = localStorage.getItem('avatax_entity_type');
            if (storedValue) {
                $('#entity_type').val(storedValue);
                localStorage.removeItem('avatax_entity_type');
            }
        })

        function refresh_mapped_table(records){
          var $select = $("#mapped_table");
          $select.empty(); // Clear existing options first
          
          $select.append($("<option></option>")
          .attr("value", "")
          .text("Select source table"));
          
          $.each(records, function(index, value) {
              $select.append($("<option></option>")
                  .attr("value", value.main_table)
                  .attr("isarray", value.isarray)
                  .text(value.main_table));
          });

          $select = $("#secondary_table");
          $select.empty(); // Clear existing options first
          
          $select.append($("<option></option>")
          .attr("value", "")
          .text("Select source table"));
          
          $.each(records, function(index, value) {
              $select.append($("<option></option>")
                  .attr("value", value.main_table)
                  .attr("isarray", value.isarray)
                  .text(value.main_table));
          });
        }
      
        function bind_delete_mapping(){
          $(".tbl_mapper_delete").unbind().bind('click', function(e){
                   var $ele = $(this).parent().parent();
                   var mapperid = $ele.children('td:first').text();
       
                   var data = {
                     action: 'wc_avatax_submit_map_perform',
                     mapperid: mapperid,
                     param: 'delete_mapper_record',
                     entity: getParameterByName('entity')
                 };
                 
                 jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                   if(response['data'] == 1){$ele.remove();}
                   refresh_tbl_mapper(response['records']);
                   showSchemaTree(JSON.parse(response['schema']), JSON.parse(response['savedSchema']))
                   refresh_mapped_table(response['mapperTables']);
                   bind_delete_mapping();
                 });
                 });
          }

          function bind_delete_conditional_mapping(){
            // code to delete conditional mapper record
          $(".tbl_condition_delete").unbind().bind('click', function(e){
            var $ele = $(this).parent().parent();
            var conditionalId = $ele.children('td:first').text();
            var filterId = $ele.children('td').eq(1).text();
            var data = {
              action: 'wc_avatax_submit_map_perform',
              conditionalId: conditionalId,
              filterId: filterId,
              param: 'delete_conditional_record'
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
              if(response['data'] == 1){$ele.remove();}
            });
          });
          }

          function refresh_tbl_mapper(records){
            $("#tbl_mapper tbody").empty();
            $("#tbl_mapper").append("<tbody>" + records + "</tbody>");
          }

          $( '.wc-avatax-help-tip' ).tipTip( {
            attribute: 'data-tip',
            fadeIn: 250,
            fadeOut: 2000,
            delay: 100,
            keepAlive: true,
          } );

  });
}).call(this);



function openCity(evt, fieldName) {
  evt.preventDefault();
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the button that opened the tab
  document.getElementById(fieldName).style.display = "block";
  //document.getElementsByClassName(fieldName).target.className  += " active";
  }
