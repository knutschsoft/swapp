<template>
    <div class="mt-3">
        <div
            class="alert alert-danger w-100 mb-0"
            role="alert"
        >
            <p
                class="font-weight-bold"
            >
                Upps! Da ist etwas schief gelaufen!
            </p>
            <p class="mb-0">
                Bitte informiere
                <a
                    :href="`mailto:${email}?subject=${subject}&body=${getBody()}`"
                    v-text="email"
                />
                über das Problem.
            </p>
        </div>
    </div>
</template>

<script>
'use strict';

export default {
    name: 'GeneralErrorAlert',
    properties: {
    },
    data: () => ({
        email: 'info@streetworkapp.de',
        subject: encodeURIComponent('Nutzermeldung zu einem Fehler in Swapp'),
    }),
    methods: {
        getBody() {
            let body = `Hallo,\r\n`;
            body += `\r\n`;
            body += `es gab einen Fehler in Swapp auf der Seite\r\n`;
            body += `${window.location.href}\r\n`;
            body += `\r\n`;
            body += `Ich hatte Folgendes getan um den Fehler zu verursachen:\r\n`;
            body += `- \r\n`;
            body += `- \r\n`;
            body += `- \r\n`;
            body += `\r\n`;
            body += `Was hattest Du erwartet, was passiert?\r\n`;
            body += `- \r\n`;
            body += `- \r\n`;
            body += `- \r\n`;
            body += `\r\n`;
            body += `Was ist stattdessen passiert?\r\n`;
            body += `- \r\n`;
            body += `- \r\n`;
            body += `\r\n`;
            body += `Bist Du in der Lage den Fehler zu reproduzieren?\r\n`;
            body += `- \r\n`;
            body += `\r\n`;
            body += `Hat Swapp dir heute geholfen? Hat es Dich in irgend einer Weise glücklich gemacht?\r\n`;
            body += `<!-- Die Beantwortung der Frage ist nicht notwendig. -->\r\n`;
            body += `<!-- Aber wenn Du etwas Positives zum Teilen hast, dann kannst Du das hier gerne tun! -->\r\n`;
            body += `<!-- Manchmal wirkt ein wenig positives Feedback Wunder. -->\r\n`;
            body += `<!-- Idea by Joey Hess, https://joeyh.name/blog/entry/two_holiday_stories/ -->\r\n`;
            body += `\r\n`;

            return encodeURIComponent(body);
        },
    }
};
</script>
