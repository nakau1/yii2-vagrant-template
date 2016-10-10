<?php
/* @var $this \app\views\View */
/* @var $error string */

use yii\helpers\Html;

$this->title = 'お客様メールアドレス入力';
$error = $error ?? null;
?>

<?php if ($error) : ?>
    <div class="alert alert-danger"><?php echo Html::encode($error); ?></div>
<?php endif; ?>
<div class="panel panel-primary">
    <div class="panel-heading">お客様メールアドレス入力</div>
    <div class="panel-body">
        <form action="cedyna-send-email-complete" method="get">
            <div class="panel panel-success">
                <div class="panel-heading">
                    カード規約と個人情報の取扱いに関する条項をご確認の上、ご連絡用のメールアドレスをご入力ください。
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label>メールアドレス</label>
                        <input class="form-control" type="email" name="email1" id="email1"
                               placeholder="ご連絡先用のメールアドレスを入力してください。">
                        <input class="form-control" type="email" name="email2" id="email2"
                               placeholder="ご確認のためもう一度入力してください。">
                    </div>
                </div>
            </div>
            <div class="panel panel-info">
                <div class="panel-heading">
                    カード会員規約
                </div>
                <div class="panel-body">
                    会員規約です。会員規約です。会員規約です。会員規約です。会員規約です。
                    会員規約です。会員規約です。会員規約です。会員規約です。会員規約です。
                    会員規約です。会員規約です。会員規約です。会員規約です。会員規約です。
                    会員規約です。会員規約です。会員規約です。会員規約です。会員規約です。
                    会員規約です。会員規約です。会員規約です。会員規約です。会員規約です。
                </div>
            </div>
            <input type="submit" value="次へ" class="btn btn-primary pull-right">
        </form>
    </div>
</div>
