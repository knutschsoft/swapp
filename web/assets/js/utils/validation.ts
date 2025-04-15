interface Violation {
    propertyPath?: string;
    message: string;
}

interface ErrorData {
    violations?: Violation[];
    data?: ErrorData;
    "description"?: string;
}

interface ValidationErrors {
    global: string[];
    [key: string]: string[] | undefined;
}

/**
 * Converts error data into a structured object containing global and field-specific errors.
 *
 * @param errorData - The error data object to process.
 * @returns A structured object containing validation errors.
 */
function getViolationErrorsObject(errorData?: ErrorData): ValidationErrors {
    const errors: ValidationErrors = {
        global: [],
    };

    let localErrorData: ErrorData | boolean | undefined = errorData;

    if (!errorData) {
        return errors;
    }

    if (errorData.data) {
        localErrorData = errorData.data;
    }

    if (localErrorData?.violations) {
        localErrorData.violations.forEach((violation) => {
            if (violation.propertyPath) {
                if (!errors[violation.propertyPath]) {
                    errors[violation.propertyPath] = [];
                }
                errors[violation.propertyPath]!.push(violation.message);
            } else {
                errors.global.push(violation.message);
            }
        });

        return errors;
    }

    if (localErrorData["description"]) {
        errors.global.push(localErrorData["description"]);
    }

    return errors;
}

/**
 * Retrieves validation feedback messages based on specified fields and error data.
 *
 * @param fields - The list of field names to include in the feedback.
 * @param errorData - The error data to process.
 * @param isResultInverted - If true, feedback will include errors not in the specified fields.
 * @returns A string containing concatenated feedback messages.
 */
function getViolationsFeedback(
    fields: string[],
    errorData?: ErrorData,
    isResultInverted: boolean = false
): string {
    const validationErrors = getViolationErrorsObject(errorData);
    let message = '';

    if (isResultInverted) {
        for (const [fieldName, errorMessageArray] of Object.entries(validationErrors)) {
            if (!fields.includes(fieldName)) {
                message += ` ${errorMessageArray!.join(' ')}`;
            }
        }
    } else {
        fields.forEach(fieldName => {
            const fieldNameArray = fieldName + '['; // e.g. conceptOfDay[0]
            for (let key in validationErrors) {
                if (key.startsWith(fieldName) || key.startsWith(fieldNameArray)) {
                    message += ` ${validationErrors[key]!.join(' ')}`;
                }
            }
        });
    }

    return message.trim();
}

export {
    type ErrorData,
    getViolationErrorsObject,
    getViolationsFeedback
};

export default getViolationsFeedback;
