function getViolationErrorsObject(errorData) {
    const errors = {
        global: [],
    };
    let localErrorData = errorData;
    if (!errorData) {
        return errors;
    }
    if (errorData.data) {
        localErrorData = errorData.data;
    }
    if (localErrorData.violations) {
        localErrorData.violations.forEach((violation) => {
            if (violation.propertyPath) {
                errors[violation.propertyPath] = violation.message
            } else {
                errors.global.push(violation.message)
            }
        });

        return errors;
    }
    if (localErrorData["hydra:description"]) {
        errors.global.push(localErrorData["hydra:description"]);
    }

    return errors;
}

function getViolationsFeedback(fields, errorData, isResultInverted = false) {
    const validationErrors = getViolationErrorsObject(errorData);
    let message = '';

    if (isResultInverted) {

        for (const [fieldName, errorMessage] of Object.entries(validationErrors)) {
            if (!fields.includes(fieldName)) {
                message += ` ${errorMessage}`;
            }
        }
    } else {
        fields.forEach(fieldName => {
            if (validationErrors[fieldName]) {
                message += ` ${validationErrors[fieldName]}`;
            }
        });
    }

    return message.trim();
}

export {
    getViolationErrorsObject,
    getViolationsFeedback
};

export default getViolationsFeedback;
