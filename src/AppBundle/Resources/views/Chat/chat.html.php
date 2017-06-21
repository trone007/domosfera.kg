<?php $view->extend('::empty.html.php') ?>
<div class="container">
    <h1>Обратная связь</h1>
    <div class="col-lg-12 text-center" style="margin-bottom: 20px;">
        <form action="/chat" method="POST">
            <table class="table table-striped">
                <tr>
                    <td>Автор</td>
                    <td colspan="2">
                        <select name="user" class="form-control">
                            <option></option>
                            <?php foreach ($users as $usr):?>
                                <option value="<?php echo $usr->getId()?>"
                                <?php if($user == $usr->getId()) {echo 'selected'; }?> >
                                    <?php echo $usr->getSurname() . ' ' . $usr->getName()?>
                                </option>
                            <?php endforeach;?>
                        </select>

                    </td>
                    <td>Период</td>
                    <td><input type="date" name="periodFrom" class="form-control" value="<?php echo $periodFrom?>"/></td>
                    <td><input type="date" name="periodTo" class="form-control" value="<?php echo $periodTo?>"/></td>
                    <td>
                        <input type="submit" class="btn btn-success" value="Найти" style="width: 150px"/>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<div class="container">
    <table class="table table-stripped table-hover">
        <tr>
            <th>Дата</th>
            <th>Артикул</th>
            <th>Пользователь</th>
            <th>Сообщение</th>
            <th>Статус</th>
            <th>Действие</th>
        </tr>

        <?php foreach ($messages as $message):?>
            <tr class="<?php echo $message->getStatus() == 1  ? 'success' : 'danger';?>">
                <td><?php echo $message->getDateTime()->format('d-m-Y');?></td>
                <td><a href="/wallpaper/<?php echo $message->getVendorCode()?>"><?php echo $message->getVendorCode();?></a></td>
                <td>
                    <?php echo $message->getUser()->getSurname() . ' ' . $message->getUser()->getName()?></td>
                <td class="col-lg-5"><?php echo htmlspecialchars($message->getMessage());?></td>
                <td><?php echo $statuses[$message->getStatus()];?></td>
                <td>
                        <?php if($message->getStatus() == 2) :?>
                            <a href="/close-message/<?php echo $message->getId();?>">Закрыть</a>
                        <?php else:?>
                            <a href="/open-message/<?php echo $message->getId();?>">Открыть</a>
                        <?php endif;?>
                </td>
            </tr>
        <?php endforeach;?>
    </table>
</div>