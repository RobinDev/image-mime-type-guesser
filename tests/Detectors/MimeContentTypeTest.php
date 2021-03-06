<?php
namespace {
    $pretendTheseFunctionDoesNotExist = [];
    $hasDeclaredMockFunction = false;
}

namespace ImageMimeTypeGuesser\Detectors {
    use Tests\ImageMimeTypeGuesser\Detectors\MimeContentTypeTest;

    global $hasDeclaredMockFunction;
    if(!$hasDeclaredMockFunction)  {
        $hasDeclaredMockFunction = true;
        function function_exists($function) {
            global $pretendTheseFunctionDoesNotExist;
            if (in_array($function, $pretendTheseFunctionDoesNotExist)) {
                return false;
            }
            return \function_exists($function);
        }
    }

    function mime_content_type() {
        if (MimeContentTypeTest::$useMock) {
            // our mock throws an exception, because that is what we need testing
            throw new \Exception('pretend something bad happened');
        } else {
            // Call original
            return call_user_func_array('\mime_content_type', func_get_args());
        }
   }

}

namespace Tests\ImageMimeTypeGuesser\Detectors {

    use \Tests\ImageMimeTypeGuesser\Detectors\AbstractDetectorTester;
    use \PHPUnit\Framework\TestCase;

    include_once 'AbstractDetectorTester.php';  // Not autoloaded, because it does not end with "Test"

    class MimeContentTypeTest extends TestCase
    {

        public static $useMock;

        public function testDoDetect()
        {
            global $pretendTheseFunctionDoesNotExist;
            $pretendTheseFunctionDoesNotExist = [];
            self::$useMock = false;
            AbstractDetectorTester::testDetect($this, 'MimeContentType');
        }

        public function testDoDetectFunctionNotExisting()
        {
            global $pretendTheseFunctionDoesNotExist;
            $pretendTheseFunctionDoesNotExist = ['mime_content_type'];
            self::$useMock = false;
            AbstractDetectorTester::testDetect($this, 'MimeContentType');
        }

        public function testDoDetectFunctionThrowingException()
        {
            global $pretendTheseFunctionDoesNotExist;
            $pretendTheseFunctionDoesNotExist = [];
            self::$useMock = true;
            AbstractDetectorTester::testDetect($this, 'MimeContentType');
        }

    }
}
