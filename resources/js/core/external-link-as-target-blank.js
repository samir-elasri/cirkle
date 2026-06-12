import $ from 'jquery';

export default function(){
    const aRegExp = new RegExp('/' + window.location.host + '/');
    const mailtoRegExp = /^mailto:[^\s]*/i;
    $('a').each((index, element) => {
        if(typeof element.href !== 'string') return true; // test first, can cause an error
        if(aRegExp.test(element.href)) return true; // same site
        if(mailtoRegExp.test(element.href)) return true; // mailto
        if(element.target) return true; // target already define
        element.target = '_blank';
    });
}
