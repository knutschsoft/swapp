import 'bootstrap';
import '../../../../../vendor/ninsuo/symfony-collection/jquery.collection';
import 'jquery-ui/ui/widgets/sortable';

var updateHrefOnSelectChange = require('./../updateHrefOnSelectChange');

updateHrefOnSelectChange('s-select-team', 's-select-team-link');
updateHrefOnSelectChange('s-select-unfinished-walk', 's-select-unfinished-walk-link');

$(function () {
    $('.js-team-type-age-ranges').collection(
        {
            add_at_the_end: false,
            fade_in: true,
            fade_out: true,
            drag_drop: true,
            drag_drop_options: {
                placeholder: 'ui-state-highlight'
            }
        }
    );
});

