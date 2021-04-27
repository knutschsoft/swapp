export default {
    formatRating: function (rating) {
        let formattedRating = '';
        let i;
        for(i = 1; i <= 5; i++){
            if (i<= rating) {
                formattedRating += '★';
            } else {
                formattedRating += '☆';
            }
        }

        return formattedRating;
    }
};
