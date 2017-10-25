<?php
/**
 * Copyright © 2016 Vicomage. All rights reserved.
 */

namespace Vicomage\Slider\Controller\Adminhtml\Items;
use Magento\Framework\App\Filesystem\DirectoryList;
class Save extends \Vicomage\Slider\Controller\Adminhtml\Items
{
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                $model = $this->_objectManager->create('Vicomage\Slider\Model\Items');
                $data = $this->getRequest()->getPostValue();
                $inputFilter = new \Zend_Filter_Input(
                    [],
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();

                $id = $this->getRequest()->getParam('id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong item is specified.'));
                    }
                }else{
                    if(isset($data['identity'])){
                        $sliderResult = $model->getCollection()->addFieldToFilter('identity' , array('eq' => $data['identity']))->getData();
                        if(isset($sliderResult[0]['id'])){
                            $this->messageManager->addError(__('Id already exists!'));
                            $this->_redirect('vicomage_slider/*/new');
                            return;
                        }
                    }
                }
				$num_data=(int)$data['number'];
                $countDel=0;

                for($i=1;$i<=$num_data;$i++)
				{
					$deleteimage="delete_image_".$i;
					if(!isset($data[$deleteimage]))
					{
						$image_name="image_".$i;
						$positon_data="position_".$i;

						$url_data="link_url_".$i;
						try{
							$imageItems[$i]['image'] = $this->uploadimage($image_name);
						}catch (\Exception $e) {
							if ($e->getCode() == 0) {
								$this->messageManager->addError($e->getMessage());
							}
							$imageItems[$i]['image']=$this->checkvalue($image_name,$data);
						}
						$imageItems[$i]['position']=$data[$positon_data];
						$imageItems[$i]['url']=$data[$url_data];
						$imageItems[$i]['description']=$data['description_'.$i];
					}
					else
					{
						$countDel+=1;
					}
				}
				//delete image
				if($countDel!=0){
                    $j=1;
                    foreach($imageItems as $valueImg)
                    {
                        $newimage[$j]=$valueImg;
                        $j++;
                    }
				}
				else
				{
					$newimage=$imageItems;
				}
                $data['slider_params'] = json_encode($newimage);
                $data["number"]=$num_data-$countDel;

                $model->setData($data);
                $session = $this->_objectManager->get('Magento\Backend\Model\Session');
                $session->setPageData($model->getData());
                $model->save();

                $this->messageManager->addSuccess(__('You saved the item.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('vicomage_slider/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('vicomage_slider/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('id');
                if (!empty($id)) {
                    $this->_redirect('vicomage_slider/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('vicomage_slider/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the item data. Please review the error log.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                $this->_redirect('vicomage_slider/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->_redirect('vicomage_slider/*/');
    }

    /**
     * @param $name
     * @return string
     */
    public function uploadimage($name)
    {
        $uploader = $this->_objectManager->create(
            'Magento\MediaStorage\Model\File\Uploader',
            ['fileId' => $name]
        );
        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
        $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

        $uploader->addValidateCallback($name, $imageAdapter, 'validateUploadFile');
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);

        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
            ->getDirectoryRead(DirectoryList::MEDIA);
        $result = $uploader->save($mediaDirectory->getAbsolutePath('/vicomage/slider'));
        $a = $result['file'];
        return $a;

    }

    /**
     * @param $name
     * @param $data
     * @return mixed
     */
	public function checkvalue($name,$data){
		if (isset($data[$name]) && isset($data[$name]['value'])) {
			if (isset($data[$name]['delete'])) {
				$data[$name] = "";
			} else if (isset($data[$name]['value'])) {
				$data[$name] = $data[$name]['value'];
			} else {
				$data[$name] = "";
			}
		}
		return $data[$name];
		
	}	
}
