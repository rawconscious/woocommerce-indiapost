import apiFetch from '@wordpress/api-fetch';

const importArticleIds = async (type, articleIds) => {

    let path = window.location.wcipSiteUrl + '/wp-json/rc-wcip/v1/import/';
    path = ('append' === type) ? path + 'append-article-ids' : path + 'overwrite-article-ids';
    let data = new FormData();

    data.append('articleId', articleIds);

    return await fetch(path, {
        method: 'POST',
        body: data,
    }).then((response) =>
        response.json()
    ).then((updateStatus) => {
        return updateStatus;
    });
}

const getArtilceIdsCount = async () => {

    return await apiFetch({ path: 'rc-wcip/v1/get-unused-article-id-count' }).then((results) => {
        return results;
    })
}

export { importArticleIds, getArtilceIdsCount };