// const BASE_URL = 'http://bookshop.sion-project.ir/';
const BASE_URL = 'http://localhost/book-shop/Codes/Back-end/';

const SUB_URL_AUTHENTICATION = 'api/authentication/';
const SUB_URL_PROFILE = 'api/profile/';
const SUB_URL_SEARCH = 'api/search/';
const SUB_URL_MESSAGE = 'api/message/';
const SUB_URL_UPLOAD = 'api/upload/';

const LOGIN_PAGE = 'logIn.php?';
const VERIFY_PAGE = 'verify.php?';
const REGISTER_PAGE = 'register.php?';
const REQUEST_VERIFICATION_CODE_PAGE = 'requestVerificationCode.php?';
const REQUEST_RESTORE_PASSWORD_CODE_PAGE = 'requestRestorePasswordCode.php?';
const RESTORE_PASSWORD_PAGE = 'restorePassword.php?';

const SEARCH_BOOKS_PAGE = 'searchBooks.php?';
const SEARCH_NOTES_PAGE = 'searchNotes.php?';
const SEARCH_REFERENCES_PAGE = 'searchReferences.php?';

const ADD_BOOK_PAGE = 'addBook.php?'
const ADD_NOTE_PAGE = 'addNote.php?'
const ADD_REFERENCE_PAGE = 'addReference.php?'
const DELETE_BOOK_PAGE = 'deleteBook.php?';
const DELETE_NOTE_PAGE = 'deleteNote.php?';
const DELETE_REFERENCE_PAGE = 'deleteReference.php?';
const EDIT_BOOK_PAGE = 'editBook.php?';
const EDIT_NOTE_PAGE = 'editNote.php?';
const EDIT_REFERENCE_PAGE = 'editReference.php?';
const EDIT_PROFILE_PAGE = 'editProfile.php?'
const GET_MY_INFO_PAGE = 'getMyInfo.php?'

const GET_CONVERSATION_MESSAGES_PAGE = 'getConversationMessages.php?'
const GET_CONVERSATIONS_PAGE = 'getConversations.php?'
const SEE_MESSAGE_PAGE = 'seeMessage.php?'
const SEND_MESSAGES_PAGE = 'sendMessage.php?'

const UPLOAD_FILE_PAGE = 'UploadFile.php?';
const UPLOAD_IMAGE_PAGE = 'UploadImage.php?';

const NAME_KEY = 'name';
const EMAIL_KEY = 'email';
const PASSWORD_KEY = 'password';
const PASSWORD_CONFIRMATION_KEY = 'password_confirmation';
const CODE_KEY = 'code';
const USER_KEY = 'user';
const MAJOR_KEY = 'major';
const USER_ID_KEY = 'user_id';
const ACCESS_TOKEN_KEY = 'access_token';
const PURCHASES_KEY = 'purchases';
const ID_KEY = 'id';
const TITLE_KEY = 'title';
const PRICE_KEY = 'price';
const DESCRIPTION_KEY = 'description';
const OWNER_KEY = 'owner';
const WRITER_KEY = 'writer';
const INSTRUCTORS_KEY = 'instructors';
const SEMESTERS_KEY = 'semester';
const COURSES_KEY = 'courses';
const MAJORS_KEY = 'majors';
const START_KEY = 'start';
const STEP_KEY = 'step';
const BOOK_KEY = 'book';
const BOOKS_KEY = 'books';
const BOOK_ID_KEY = 'book_id';
const BOOK_IDS_KEY = 'book_ids';
const NOTE_KEY = 'note';
const NOTES_KEY = 'notes';
const NOTE_ID_KEY = 'note_id';
const NOTE_IDS_KEY = 'note_ids';
const REFERENCES_KEY = 'references';
const REFERENCE_KEY = 'reference';
const SIZE_KEY = 'size';
const MESSAGE_KEY = 'message';
const MESSAGES_KEY = 'messages';
const CONVERSATIONS_KEY = 'conversations';
const CONVERSATION_ID_KEY = 'conversation_id';
const PARTICIPANT_1_KEY = 'participant1';
const PARTICIPANT_2_KEY = 'participant2';
const RECEIVER_KEY = 'receiver';
const CONTENT_KEY = 'content';
const DIRECTION_KEY = 'direction';
const FILES_KEY = 'files';
const IMAGES_KEY = 'images';
const TAGS_KEY = 'tags';
const IMAGE_KEY = 'image';
const IMAGE_ID_KEY = 'image_id';
const IMAGE_URL_KEY = 'image_url';
const FILE_KEY = 'file';
const FILE_ID_KEY = 'file_id';
const MESSAGE_IN_DOCUMENT = 'message_in_document';
const MESSAGE_IN_PROFILE = 'message_in_profile';
const LINK_KEY = 'link';

const USER_ID_KEY_SOTRAGE = '982KBS_user_id';
const TOKEN_KEY_SOTRAGE = '982KBS_token';
const EMAIL_KEY_SOTRAGE = '982KBS_email';
const NAME_KEY_SOTRAGE = '982KBS_name';
const PURCHASES_KEY_STORAGE = '982KBS_purchases';
const PASSWORD_KEY_SOTRAGE = '982KBS_password';
const PASSWORD_CONFIRM_KEY_SOTRAGE = '982KBS_password_confirm';
const MAJOR_KEY_SOTRAGE = '982KBS_major';
const PRODUCT_TYPE_KEY_STORAGE = '982KBS_product_type';
const PRODUCT_OBJECT_KEY_STORAGE = '982KBS_product_object';
const SEARCH_TITLE_KEY_STORAGE = '982KBS_search_title';
const SEARCH_TYPE_KEY_STORAGE = '982KBS_search_type';
const DELETE_PRODUCT_ID_KEY_STORAGE = '982KBS_delete_product_id';
const DELETE_PRODUCT_TYPE_KEY_STORAGE = '982KBS_delete_product_type';
const EDIT_PRODUCT_KEY_STORAGE = '982KBS_edit_product';
const EDITING_MODE_KEY_STORAGE = '982KBS_editing_mode';
const NUMBER_OF_IMAGES_IN_EDITING_MODE_KEY_STORAGE = '982KBS_number_of_images_in_editing_mode';
const RECEIVER_ID_KEY_STORAGE = '982KBS_receiver_id';

const MODAL_DELETE_DOCUMENTS_ID = 'modal_DeleteDocuments';
const MODAL_SUCCESSFUL_MESSAGE_ID = 'modal_SuccessfulMessage';
const MODAL_UNSUCCESSFUL_MESSAGE_ID = 'modal_UnSuccessfulMessage';
const MODAL_MAKE_NOTE_ID = 'modal_MakeNote';
const MODAL_MAKE_BOOK_ID = 'modal_MakeBook';
const MODAL_MESSAGES_ID = 'modal_Messages';
const MODAL_DETAILS_MESSAGE_ID = 'modal_details_message';
const MODAL_CHANGE_PASSWORD_ID = 'modal_change_password';
const MODAL_EDIT_PROFILE_ID = 'modal_edit_profile';

const SUCCESSFUL_MODAL_TYPE = 'successful';
const UNSUCCESSFUL_MODAL_TYPE = 'unsuccessful';

const DELETE_MESSAGE = 'آیتم مورد نظر با موفقیت حذف شد.';
const ADD_BOOK_TITTLE = 'اضافه کردن کتاب';
const ADD_NOTE_TITTLE = 'اضافه کردن جزوه';
const EDIT_BOOK_TITTLE = 'ویرایش کتاب';
const EDIT_NOTE_TITTLE = 'ویرایش جزوه';
const SUCCESSFUL_ADD_BOOK = 'کتاب شما باموفقیت به سایت اضافه شد.';
const UNSUCCESSFUL_ADD_BOOK = '!متاسفانه، کتاب شما به سایت اضافه نشد.';
const SUCCESSFUL_ADD_NOTE = 'جزوه شما باموفقیت به سایت اضافه شد.';
const UNSUCCESSFUL_ADD_NOTE = '!متاسفانه، جزوه شما به سایت اضافه نشد.';
const SUCCESSFUL_EDIT_BOOK = 'کتاب شما باموفقیت ویرایش شد.';
const UNSUCCESSFUL_EDIT_BOOK = '!متاسفانه، کتاب شما ویرایش نشد.';
const SUCCESSFUL_EDIT_NOTE = 'جزوه شما باموفقیت ویرایش شد.';
const UNSUCCESSFUL_EDIT_NOTE = '!متاسفانه، جزوه شما ویرایش نشد.';
const UNSUCCESSFUL_NOT_FOUND_BOOK = 'متاسفانه کتابی با این عنوان یافت نشد!';
const UNSUCCESSFUL_NOT_FOUND_NOTE = 'متاسفانه جزوه‌ای با این عنوان یافت نشد!';
const UNSUCCESSFUL_NOT_FOUND_REFERENCE = 'متاسفانه منبعی با این عنوان یافت نشد!';
const SUCCESSFUL_CHANGE_PASSWORD = 'رمزعبور شما باموفقیت تغییر یافت.';
const UNSUCCESSFUL_CHANGE_PASSWORD = 'متاسفانه رمز عبور شما تغییر نیافت.';
const SUCCESSFUL_EDIT_PROFILE = 'مشخصات مورد نظر شما با موفقیت تغییر یافت.';
const UNSUCCESSFUL_EDIT_PROFILE = 'متاسفانه مشخصات شما تغییر نیافت!';
const ERROR_IN_CONNECT_WITH_SERVER = 'خطا در برقراری ارتباط با سرور';
const SUCCESSFUL_PAYMENT = 'پرداخت شما موفقیت آمیز بود.';
const UNSUCCESSFUL_PAYMENT = 'متاسفانه پرداخت شما موفقیت آمیز نبود!';
const YOU_MUST_LOGIN = 'برای جستجو باید وارد سایت شوید.';