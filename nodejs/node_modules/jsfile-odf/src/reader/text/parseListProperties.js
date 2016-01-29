const listStyleTypes = {
    1: 'decimal',
    i: 'lower-roman',
    I: 'upper-roman',
    a: 'lower-alpha',
    A: 'upper-alpha'
};

export default function (xml) {
    const result = {
        style: {}
    };
    const nodes = xml && xml.childNodes || [];
    let i = nodes.length;

    while (i--) {
        if (nodes[i].localName === 'list-level-style-number') {
            let attr = nodes[i].attributes['style:num-format'];

            result.style.listStyleType = (attr && listStyleTypes[attr.value]) || 'auto';
        }
    }

    return result;
};