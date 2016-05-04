jQuery(function($) {
    window.initImport = function() {
        aggregion.init();
    };

    var aggregion = {
        init: function() {
            var agg = this;

            agg.current = 0;
            agg.done = 0;
            agg.$progressBar = $('#progress-bar');
            agg.$itemsCount = $('#itemsCount');
            agg.$items = $('#items');

            agg.getItems();
        },
        start: function(items) {
            var agg = this;

            if (items) {
                var parsed_items = JSON.parse(items);

                if (parsed_items) {
                    agg.total = parsed_items.length;

                    $.each(parsed_items, function(i, e) {
                        agg.importItem(e);
                    });
                }
            }
        },

        importItem: function(item) {
            var agg = this;
            var url = '/administrator/index.php?option=com_catalogue&task=aggregion.importItem&tmpl=raw',
                data = {item: JSON.stringify(item)};

            $.post(url, data, function (response) {
                var data = JSON.parse(response);

                agg.step(data);
            });
        },

        step: function(data) {

            var agg = this;

            agg.done++;

            if(data.status !== 0) {
                // handle failure
                console.log(data.msg);

                //return;
            } else {
                console.log(data.item);
                var item_base_url = '/administrator/index.php?option=com_catalogue&view=item&layout=edit&id=';
                agg.$items.append(
                    $('<div>', {
                        class: 'agg-import-item'
                    }).text(data.item.title)
                );
            }

            if( data.Warnings ) {
                // @todo Handle warnings
                /**
                 $.each(data.Warnings, function(i, item){
                $('#warnings').append(
                    $(document.createElement('div'))
                        .html(item)
                );
                $('#warningsBox').show('fast');
            });
                 /**/
            }

            // Add data to variables
            var percent = agg.done * 100 / agg.total;

            // Update GUI

            agg.$progressBar.css('width', percent + '%').attr('aria-valuenow', percent);

            if (percent >= 100) {
                agg.endImport();
            }

            if(agg.total === agg.done)
            {
                //finalizeUpdate();
                console.log('Done!');
            }

            agg.$itemsCount.text(agg.done + ' / ' + agg.total);
        },

        getItems: function() {
            var url = '/administrator/index.php?option=com_catalogue&task=aggregion.getItemsAJAX&tmpl=raw';
            var agg = this;
            $.get(url, function(items) {
                agg.start(items);
            });
        },

        endImport: function() {
            var agg = this;
            agg.$progressBar.removeClass('bar-success');
            $('#finishImportForm').submit();
        }
    }
});