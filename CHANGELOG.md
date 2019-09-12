# Changelog

v3.1.0 20xx-xx-xx
-------
chores:
 * changed a lot of classes to "final". Theoretically [tm] this should not break your application. All moved classes
   where meant to be internal classes, used only by PHPTAL itself. If something breaks, please open a ticket with your
   usecase!
 * removed unused functions which should not have been called from outside of PHPTAL. These are:
   - Defs::namespaceURIToPrefix
   - Defs::getPredefinedPrefixes
   - Element::hasAttribute
   - SaxXmlParser::getSourceFile
   - SaxXmlParser::getLineNumber
   - DefaultKeyword::jsonSerialize
   - NothingKeyword::jsonSerialize
   - TalNamespace::hasAttribute
   - TalNamepsace::getAttribute
 * introducing some interfaces:
   - CodeWriterInterface
   - StateInterface
   - TalesChainExecutorInterface

v3.0.2 2019-09-09
-------
chores:
 * issue 70: add changelog and update version tag in source
 * issue 73: fix some code smells and add a contribution manual
 * issue 75: update phpunit to 8.x (and fix all the deprecation issues)
 * issue 78: clean up code fragments after adding type hints to almost everything
             - PHPTAL::addPreFilter no longer accepts FilterInterface but requires an instance of PreFilter
             - PHPTAL::setPhpCodeDestination now returns void instead of self
             - SourceInterface::getLastModifiedTime now returns float instead of int

v3.0.1 2019-04-09
-------
bugfixes:
 * issue #62: FillSlot::estimateNumberOfBytesOutput must return float
 * issue #63: Misinterpreted continue statement in Transformer

chores:
 * issue #66: add php7.3 to travis.yml
 * issue #67: remove explicit php version from doc blocks
