const { registerBlockType } = wp.blocks;
const { MediaUpload, MediaUploadCheck, InspectorControls } = wp.blockEditor || wp.editor;
const { Button, PanelBody } = wp.components;
const { useSelect, useDispatch } = wp.data;
const { useEffect } = wp.element;

registerBlockType('marketplace/review-images', {
    title: 'Review Images',
    icon: 'format-gallery',
    category: 'common',
    attributes: {
        images: {
            type: 'array',
            default: [],
        },
    },
    edit: (props) => {
        const { attributes: { images }, setAttributes, clientId } = props;

        // Записываем в мета при изменении (для совместимости с meta-полем review_images)
        useEffect(() => {
            wp.data.dispatch('core/editor').editPost({
                meta: { review_images: images }
            });
        }, [images]);

        return (
            <>
                <InspectorControls>
                    <PanelBody title="Загруженные фото">
                        {images && images.length > 0 ? (
                            <ul>
                                {images.map(img => (
                                    <li key={img.id}>{img.url}</li>
                                ))}
                            </ul>
                        ) : (
                            <p>Нет фото</p>
                        )}
                    </PanelBody>
                </InspectorControls>
                <div className="review-images-block">
                    <MediaUploadCheck>
                        <MediaUpload
                            onSelect={(newImgs) => {
                                const imgs = Array.isArray(newImgs) ? newImgs : [newImgs];
                                setAttributes({ images: imgs.map(img => ({ id: img.id, url: img.url })) });
                            }}
                            allowedTypes={['image']}
                            multiple
                            gallery
                            value={images.map(img => img.id)}
                            render={({ open }) => (
                                <Button onClick={open} isPrimary>
                                    {images.length ? 'Изменить фото' : 'Добавить фото'}
                                </Button>
                            )}
                        />
                    </MediaUploadCheck>
                    {images.length > 0 && (
                        <div className="review-images-gallery" style={{ marginTop: 16, display: 'flex', gap: 8 }}>
                            {images.map((img, idx) => (
                                <div key={img.id} style={{ position: 'relative' }}>
                                    <img src={img.url} style={{ width: 80, height: 80, objectFit: 'cover', borderRadius: 6, border: '1px solid #eee' }} />
                                    <Button
                                        icon="no-alt"
                                        style={{
                                            position: 'absolute', top: 0, right: 0, background: '#fff', color: 'red', border: 'none'
                                        }}
                                        onClick={() => {
                                            setAttributes({
                                                images: images.filter((_, i) => i !== idx)
                                            });
                                        }}
                                    />
                                </div>
                            ))}
                        </div>
                    )}
                </div>
            </>
        );
    },
    save: () => null, // Только для редактора, не для фронта
});
