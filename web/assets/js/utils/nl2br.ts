export const nl2br = (text: string ) => {
    const breakTag = '<br>';
    const replaceStr = '$1' + breakTag;

    return (text + '').trim().replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
}
