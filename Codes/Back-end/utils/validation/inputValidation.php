<?php

function isAccessTokenValid($token)
{
    if (is_string($token)) {
        return strlen($token) == 128;
    }
    return false;
}

function isConversationIdValid($conversationId)
{
    return isInteger($conversationId);
}

function isCourseIdValid($courseId)
{
    return isInteger($courseId);
}

function isDescriptionValid($description)
{
    return validateString($description, 201);
}

function isEmailValid($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isFileIdValid($fileId)
{
    return isInteger($fileId);
}

function isImageIdValid($imageId)
{
    return isInteger($imageId);
}

function isInstructorIdValid($instructor)
{
    return isInteger($instructor);
}

function isMajorIdValid($major)
{
    return isInteger($major);
}

function isMessageIdValid($messageId)
{
    return isInteger($messageId);
}

function isMessageValid($message) // message = content
{
    return validateString($message, 151);

}

function isNameValid($name)
{
    return validateString($name, 21);

}

function isPasswordValid($password)
{
    return validateString($password, 257);

}

function isPasswordAndPasswordConfirmationMatch($passwordInput, $passwordConfirmationInput)
{
    return $passwordInput === $passwordConfirmationInput;
}

function isPaymentValid($payment)
{
    return isInteger($payment) && strlen(strval($payment)) == 16;
}

function isPriceValid($price)
{
    return isInteger($price);
}

function isRestorePasswordCodeValid($restorePasswordCode)
{
    return validateString($restorePasswordCode, 11);
}

function isStartValid($start)
{
    return isInteger($start);
}

function isStepValid($step)
{
    return isInteger($step);
}

function isTagValid($tag)
{
    if (is_string($tag) && strlen($tag) > 1) {
        $identifier = substr($tag, 0, 1);
        $tagID = substr($tag, 1);
        if (($identifier == 'm' || $identifier == 'M' ||
                $identifier == 's' || $identifier == 'S' ||
                $identifier == 'c' || $identifier == 'C' ||
                $identifier == 'i' || $identifier == 'I') &&
            isInteger($tagID)) return true;
    }
    return false;
}

function isTitleValid($title)
{
    return validateString($title, 51);

}

function isUserIdValid($userId)
{
    return isInteger($userId);
}

function isVerificationCodeValid($verificationCode)
{
    return validateString($verificationCode, 11);
}

function isWriterValid($writer)
{
    return validateString($writer, 71);

}

function isBookIdValid($bookId)
{
    return isInteger($bookId);
}

function isReferenceIdValid($referenceId)
{
    return isInteger($referenceId);
}

function isNoteIdValid($noteId)
{
    return isInteger($noteId);
}

function isInteger($id)
{
    if (is_numeric($id) && strlen(strval($id)) > 0)
        return is_int($id + 0);
    return false;
}

function validateString($str, $maxLength)
{
    if (is_string($str)) {
        $length = mb_strlen(trim($str));
        return $length > 0 && $length < $maxLength;
    }
    return false;

}