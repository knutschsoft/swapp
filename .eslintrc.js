// .eslintrc.js
module.exports = {
    "env": {
        "browser": true,
        "es6": true
    },
    extends: [
        // "eslint:recommended",
        // "plugin:vue/essential",
        // "plugin:vue/strongly-recommended",
        "plugin:vue/recommended",
    ],
    rules: {
        "vue/html-indent": ["error", 4, {
            "attribute": 1,
            "closeBracket": 0,
            "alignAttributesVertically": true,
            "ignores": []
        }]
    },
    "parserOptions": {
        "ecmaVersion": 2018,
        "sourceType": "module",
        "parser": "babel-eslint"
    },
};
